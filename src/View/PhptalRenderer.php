<?php

namespace ZfPhptal\View;

use Zend\View\Renderer\RendererInterface;
use Zend\View\Renderer\TreeRendererInterface;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\View as ZendView;
use Zend\View\HelperPluginManager;
use PHPTAL as PhptalEngine;
use Zend\View\Exception;

class PhptalRenderer  implements RendererInterface, TreeRendererInterface
{
    /**
     * @var bool
     */
    protected $canRenderTrees = false;
    
    /**
     * @var PhptalEngine
     */
    protected $engine;
    
    /**
     * @var ResolverInterface
     */
    protected $resolver;
    
    /**
     * @var ZendView
     */
    protected $view;
    
    /**
     * @var HelperPluginManager
     */
    protected $helperPluginManager;
    
    /**
     * @var array Cache of plugins.
     */
    protected $__pluginsCache;
    
    /**
     * @param ZendView $view
     * @param PhptalEngine $engine
     * @param ResolverInterface $resolver
     */
    public function __construct(
        ZendView $view,
        PhptalEngine $engine,
        ResolverInterface $resolver
    ) {
        $this->view = $view;
        $this->engine = $engine;
        $this->resolver = $resolver;
    }
    
    public function getEngine()
    {
        return $this->engine;
    }

    public function render($nameOrModel, $values = null)
    {
        $model = null;
        if ($nameOrModel instanceof ModelInterface) {
            $model = $nameOrModel;
            $nameOrModel = $model->getTemplate();
            if (empty($nameOrModel)) {
                throw new Exception\DomainException(sprintf(
                    '%s: received View Model, but template is empty',
                    __METHOD__
                ));
            }
            $options = $model->getOptions();
            foreach ($options as $setting => $value) {
                $method = 'set' . $setting;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
                unset($method, $setting, $value);
            }
            unset($options);
            $values = (array)$model->getVariables();
        }
        
        if (!$this->canRender($nameOrModel)) {
            return null;
        }

        // handle tree rendering
        if ($model && $this->canRenderTrees() && $model->hasChildren()) {
            if (!isset($values['content'])) {
                $values['content'] = '';
            }
            foreach ($model as $child) {
                /** @var \Zend\View\Model\ViewModel $child */
                if ($this->canRender($child->getTemplate())) {
                    $file = $this->resolver->resolve(
                        $child->getTemplate(),
                        $this
                    );
                    $this->engine->setTemplateRepository(dirname($file));
                    $childVariables = (array) $child->getVariables();
                    $childVariables['this'] = $this;
                    foreach ($childVariables as $key => $value) {
                        $this->engine->set($key, $value);
                    }
                    $this->engine->setTemplate($file);
                    return $this->engine->execute();
                }
                $child->setOption('has_parent', true);
                $values['content'] .= $this->view->render($child);
            }
        }
        
        // give the template awareness of the Renderer
        $values['this'] = $this;
        // assign the variables
        foreach ($values as $key => $value) {
            $this->engine->set($key, $value);
        }
        // resolve the template
        $file = $this->resolver->resolve($nameOrModel);
        $this->engine->setTemplateRepository(dirname($file));
        
        // Setup a collection of standard variable available in the view
        $this->engine->set('doctype', $this->plugin('Doctype'));
        $this->engine->set('headTitle', $this->plugin('HeadTitle'));
        $this->engine->set('headScript', $this->plugin('HeadScript'));
        $this->engine->set('headLink', $this->plugin('HeadLink'));
        $this->engine->set('headMeta', $this->plugin('HeadMeta'));
        $this->engine->set('headStyle', $this->plugin('HeadStyle'));
        
        // render
        $this->engine->setTemplate($file);
        return $this->engine->execute();
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @param  ResolverInterface $resolver
     * @return RendererInterface
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }
    
    /**
     * Indicate whether the renderer is capable of rendering trees of view models
     *
     * @return bool
     */
    public function canRenderTrees()
    {
        return $this->canRenderTrees;
    }
    
    /**
     * @param $canRenderTrees
     * @return self
     */
    public function setCanRenderTrees($canRenderTrees)
    {
        $this->canRenderTrees = $canRenderTrees;
        return $this;
    }

    /**
     * Can the template be rendered?
     * A template can be rendered if the attached resolver can resolve the given template name.
     * 
     * @param $name
     * @return bool
     */
    public function canRender($name)
    {
        $resolvedName = $this->resolver->resolve($name);
        return false !== $resolvedName;
    }
    
    /**
     * @return HelperPluginManager
     */
    public function getHelperPluginManager()
    {
        return $this->helperPluginManager;
    }
    
    /**
     * Sets the HelperPluginManagers Renderer instance to $this.
     * @param HelperPluginManager $helperPluginManager
     */
    public function setHelperPluginManager(HelperPluginManager $helperPluginManager)
    {
        $helperPluginManager->setRenderer($this);
        $this->helperPluginManager = $helperPluginManager;
    }
    
    /**
     * Retrieve plugin instance.
     *
     * Proxies to HelperPluginManager::get.
     *
     * @param string $name Plugin name.
     * @param array $options Plugin options. Passed to the plugin constructor.
     * @return \Zend\View\Helper\AbstractHelper
     */
    public function plugin($name, array $options = null)
    {
        return $this->getHelperPluginManager()
            ->setRenderer($this)
            ->get($name, $options);
    }
    
    /**
     * Clone PHPTAL engine.
     */
    public function __clone()
    {
        $this->engine = clone $this->engine;
        $this->engine->set('this', $this);
    }
    
    /**
     * Magic method overloading
     *
     * Proxies calls to the attached HelperPluginManager.
     * * Helpers without an __invoke() method are simply returned.
     * * Helpers with an __invoke() method will be called and their return
     *   value is returned.
     *
     * A cache is used to speed up successive calls to the same helper.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->__pluginsCache[$name])) {
            $this->__pluginsCache[$name] = $this->plugin($name);
        }
        if (is_callable($this->__pluginsCache[$name])) {
            return call_user_func_array($this->__pluginsCache[$name], $arguments);
        }
        return $this->__pluginsCache[$name];
    }
}
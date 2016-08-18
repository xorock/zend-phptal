<?php

namespace ZfPhptal\View;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\View as ZendView;
use PHPTAL as PhptalEngine;
use ZfPhptal\Phptal\PhptalResolver;

class PhptalRendererFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return PhptalRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $renderer = new PhptalRenderer(
            $container->get(ZendView::class),
            $container->get(PhptalEngine::class),
            $container->get(PhptalResolver::class)
        );
        $renderer->setHelperPluginManager($container->get('ViewHelperManager'));
        return $renderer;
    }
}

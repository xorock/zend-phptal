<?php

namespace ZfPhptal\Phptal;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Resolver\TemplatePathStack;
use ZfPhptal\ModuleOptions;

class TemplatePathStackFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return TemplatePathStack
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options ModuleOptions */
        $config = $container->has(ModuleOptions::class) ? $container->get(ModuleOptions::class) : [];

        /* @var $templatePathStack TemplatePathStack */
        $templatePathStack = $container->get('ViewTemplatePathStack');
        $templatePathStack->setDefaultSuffix($config->getExtension());
        return $templatePathStack;
    }
}
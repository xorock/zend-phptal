<?php

namespace ZfPhptal\Phptal;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Resolver\TemplateMapResolver;
use ZfPhptal\ModuleOptions;

class TemplateMapResolverFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return TemplateMapResolver
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options ModuleOptions */
        $config = $container->has(ModuleOptions::class) ? $container->get(ModuleOptions::class) : [];
        
        /* @var $templateMap TemplateMapResolver */
        $templateMap = $container->get('ViewTemplateMapResolver');
        // build map of template files with registered extension
        $map = [];
        foreach ($templateMap as $name => $path) {
            if ($config->getExtension() == pathinfo($path, PATHINFO_EXTENSION)) {
                $map[$name] = $path;
            }
        }
        return new TemplateMapResolver($map);
    }
}
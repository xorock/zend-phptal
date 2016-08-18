<?php

namespace ZfPhptal;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create an object
     * 
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->has('Configuration') ? $container->get('Configuration') : [];
        $config = isset($config['zfphptal']) ? $config['zfphptal'] : [];

        return new ModuleOptions($config);
    }
}
<?php

namespace ZfPhptal\Phptal;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfPhptal\Phptal\TemplateMapResolver;
use ZfPhptal\Phptal\TemplatePathStack;
use Zend\View\Resolver\AggregateResolver;

class PhptalResolverFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AggregateResolver
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $resolver = new AggregateResolver();
        $resolver->attach($container->get(TemplatePathStack::class));
        $resolver->attach($container->get(TemplateMapResolver::class));
        return $resolver;
    }
}
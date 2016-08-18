<?php

namespace ZfPhptal\View;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfPhptal\View\PhptalRenderer;

class PhptalStrategyFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return PhptalRenderer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PhptalStrategy($container->get(PhptalRenderer::class));
    }
}
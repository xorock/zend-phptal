<?php

use PHPTAL as PhptalEngine;
use ZfPhptal\View\PhptalEngineFactory;
use ZfPhptal\ModuleOptions;
use ZfPhptal\ModuleOptionsFactory;
use ZfPhptal\View\PhptalStrategy;
use ZfPhptal\View\PhptalStrategyFactory;
use ZfPhptal\View\PhptalRenderer;
use ZfPhptal\View\PhptalRendererFactory;
use ZfPhptal\Phptal\PhptalResolver;
use ZfPhptal\Phptal\PhptalResolverFactory;
use ZfPhptal\Phptal\TemplateMapResolver;
use ZfPhptal\Phptal\TemplateMapResolverFactory;
use ZfPhptal\Phptal\TemplatePathStack;
use ZfPhptal\Phptal\TemplatePathStackFactory;

return [
    'zfphptal' => [
        'extension' => 'html',
        'cache_dir' => 'data/cache/phptal',
        // if enabled, delete all template cache files before processing
        'cache_purge_mode' => false,
        // set how long compiled templates and phptal:cache files are kept; in days 
        'cache_lifetime' => 30,
        'encoding' => 'UTF-8',
        // one of the predefined constants: PHPTAL::HTML5,  PHPTAL::XML, PHPTAL::XHTML
        'output_mode' => PhptalEngine::HTML5,
        // set whitespace compression mode
        'compress_whitespace' => false,
        // strip all html comments
        'strip_comments' => false,
    ],
    
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            PhptalStrategy::class => PhptalStrategyFactory::class,
            PhptalRenderer::class => PhptalRendererFactory::class,
            TemplateMapResolver::class => TemplateMapResolverFactory::class,
            TemplatePathStack::class => TemplatePathStackFactory::class,
            PhptalResolver::class => PhptalResolverFactory::class,
            PhptalEngine::class => PhptalEngineFactory::class,
        ]
    ],
    
    'view_manager' => [
        'strategies' => [
            PhptalStrategy::class
        ]
    ],
];
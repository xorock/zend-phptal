# PHPTAL Integration for Zend Framework 3

Provides [PHPTAL](http://phptal.org/) integration for
[Expressive](https://github.com/zendframework/zendframework).

## Installation

Install this library using composer:

```bash
$ composer require xorock/zend-phptal
```

## Configuration

The following configuration options, specific to PHPTAL, is consumed by Service Factory:

```php

return [
    'zfphptal' => [
        'cache_dir' => 'path to cached templates',
        // if enabled, delete all template cache files before processing
        'cache_purge_mode' => boolean,
        // set how long compiled templates and phptal:cache files are kept; in days 
        'cache_lifetime' => 30,
        'encoding' => 'set input and ouput encoding; defaults to UTF-8',
        // one of the predefined constants: PHPTAL::HTML5,  PHPTAL::XML, PHPTAL::XHTML
        'output_mode' => PhptalEngine::HTML5,
        // set whitespace compression mode
        'compress_whitespace' => boolean,
        // strip all html comments
        'strip_comments' => boolean,
        // if enabled, forces to reparse templates every time
        'debug' => boolean,
    ],
];
```

## Using Zend Framework View Helpers

PhptalRenderer proxies to HelperPluginManager by 
`public function plugin($name, array $options = null)` or directly with `__call()`:

```php
<a tal:attributes="href php: this.url('sample_route')">link</a>
```

You can register own plugins in global / module config using ZF 'view_helpers' option key.
For example, to register `Test` plugin:

**module.config.php**

```php
return [
    'view_helpers' => [
        'aliases' => [
            'test' => \My\View\Helper\Test::class,
        ],
        'factories' => [
            \My\View\Helper\Test::class => \Zend\ServiceManager\Factory\InvokableFactory::class
        ],
    ],
];


// inside template
// ${php: this.test()}
```

<?php

namespace ZfPhptal\View;

use DirectoryIterator;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use PHPTAL as PhptalEngine;
use PHPTAL_PreFilter_Compress;
use PHPTAL_PreFilter_StripComments;
use PHPTAL_TalesRegistry;
use ZfPhptal\Exception;
use ZfPhptal\ModuleOptions;

/**
 * Create and return a PHPTAL engine instance.
 *
 * This factory consumes the following structure:
 *
 * <code>
 * 'phptal' => [
 *     'cache_dir' => 'path to cached templates',
 *     // if enabled, delete all template cache files before processing
 *     'cache_purge_mode' => boolean,
 *     // set how long compiled templates and phptal:cache files are kept; in days 
 *     'cache_lifetime' => 30,
 *     'encoding' => 'set input and ouput encoding; defaults to UTF-8',
 *     // one of the predefined constant: PHPTAL::HTML5,  PHPTAL::XML, PHPTAL::XHTML
 *     'output_mode' => PHPTAL::HTML5,
 *     // set whitespace compression mode
 *     'compress_whitespace' => boolean,
 *     // strip all html comments
 *     'strip_comments' => boolean,
 *     // if enabled, forces to reparse templates every time
 *     'debug' => boolean,
 * ],
 * </code>
 */
class PhptalEngineFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @return PhptalEngine
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->has(ModuleOptions::class) ? $container->get(ModuleOptions::class) : [];
        
        if (! $config instanceof ModuleOptions) {
            throw new Exception\InvalidConfigException(sprintf(
                '"config" service must be ModuleOptions for the %s to be able to consume it; received %s',
                __CLASS__,
                (is_object($config) ? get_class($config) : gettype($config))
            ));
        }
        
        // Create the engine instance:
        $engine = new PhptalEngine();
        
        // Change the compiled code destination if set in the config
        if (!empty($config->getCacheDir())) {
            $engine->setPhpCodeDestination($config->getCacheDir());
        }
        
        // Configure the encoding
        if (!empty($config->getEncoding())) {
            $engine->setEncoding($config->getEncoding());
        }
        
        // Configure the output mode
        $outputMode = !empty($config->getOutputMode()) ? $config->getOutputMode() : PhptalEngine::HTML5;
        $engine->setOutputMode($outputMode);
        
        // Set template repositories
//        if (isset($config['paths'])) {
//            $engine->setTemplateRepository($config['paths']);
//        }
        
        // Configure cache lifetime
        if (!empty($config->getCacheLifetime())) {
            $engine->setCacheLifetime($config->getCacheLifetime());
        }
        
        // If purging of the tal template cache is enabled
        // find all template cache files and delete them        
        if ($config->getCachePurgeMode()) {
            $cacheFolder = $engine->getPhpCodeDestination();
            if (is_dir($cacheFolder)) {
                foreach (new DirectoryIterator($cacheFolder) as $cacheItem) {
                    if (strncmp($cacheItem->getFilename(), 'tpl_', 4) != 0 || $cacheItem->isdir()) {
                        continue;
                    }
                    @unlink($cacheItem->getPathname());
                }
            }
        }

        // Configure the whitespace compression mode        
        if ($config->getCompressWhitespace()) {
            $engine->addPreFilter(new PHPTAL_PreFilter_Compress());
        }
        
        // Strip html comments and compress un-needed whitespace
        if ($config->getStripComments()) {
            $engine->addPreFilter(new PHPTAL_PreFilter_StripComments());
        }
        
        if ($config->getDebug()) {
            $engine->setForceReparse(true);
        }
        
        return $engine;
    }
}

<?php

namespace ZfPhptal;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $extension;
    
    /**
     * @var string
     */
    protected $cacheDir;
    
    /**
     * @var boolean
     */
    protected $cachePurgeMode;
    
    /**
     * @var int
     */
    protected $cacheLifetime;
    
    /**
     * @var string
     */
    protected $encoding;
    
    /**
     * @var int
     */
    protected $outputMode;
    
    /**
     * @var boolean
     */
    protected $compressWhitespace;
    
    /**
     * @var boolean
     */
    protected $stripComments;
    
    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
    
    /**
     * @param string $extension
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }

    public function getCachePurgeMode()
    {
        return $this->cachePurgeMode;
    }

    public function setCachePurgeMode($cachePurgeMode)
    {
        $this->cachePurgeMode = is_bool($cachePurgeMode);
        return $this;
    }

    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = (int) $cacheLifetime;
        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getOutputMode()
    {
        return $this->outputMode;
    }

    public function setOutputMode($outputMode)
    {
        $this->outputMode = $outputMode;
        return $this;
    }

    public function getCompressWhitespace()
    {
        return $this->compressWhitespace;
    }

    public function setCompressWhitespace($compressWhitespace)
    {
        $this->compressWhitespace = is_bool($compressWhitespace);
        return $this;
    }

    public function getStripComments()
    {
        return $this->stripComments;
    }

    public function setStripComments($stripComments)
    {
        $this->stripComments = is_bool($stripComments);
        return $this;
    }
    
    public function getDebug()
    {
        return $this->debug;
    }

    public function setDebug($debug)
    {
        $this->debug = is_bool($debug);
        return $this;
    }
}
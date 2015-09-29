<?php

namespace CodeTool\ArtifactDownloader\Config;

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    private $scopesConfigs;

    /**
     * @param string                 $version
     * @param string                 $timestamp
     * @param ScopeConfigInterface[] $scopesConfigs
     */
    public function __construct($version, $timestamp, array $scopesConfigs)
    {
        $this->version = $version;
        $this->timestamp = $timestamp;
        $this->scopesConfigs = $scopesConfigs;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    public function getScopesConfig()
    {
        return $this->scopesConfigs;
    }
}

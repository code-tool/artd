<?php

namespace CodeTool\ArtifactDownloader\Config;

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $revision;

    /**
     * @var string
     */
    private $version;

    /**
     * @var \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    private $scopesConfigs;

    /**
     * @param string                 $revision
     * @param string                 $version
     * @param ScopeConfigInterface[] $scopesConfigs
     */
    public function __construct($revision, $version, array $scopesConfigs)
    {
        $this->revision = $revision;
        $this->version = $version;
        $this->scopesConfigs = $scopesConfigs;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    public function getScopesConfig()
    {
        return $this->scopesConfigs;
    }
}

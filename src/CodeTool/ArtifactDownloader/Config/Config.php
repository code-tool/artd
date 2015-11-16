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
     * @var \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    private $scopesConfigs;

    /**
     * @param string                 $revision
     * @param ScopeConfigInterface[] $scopesConfigs
     */
    public function __construct($revision, array $scopesConfigs)
    {
        $this->revision = $revision;
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
     * @return \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    public function getScopesConfig()
    {
        return $this->scopesConfigs;
    }
}

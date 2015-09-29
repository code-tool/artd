<?php

namespace CodeTool\ArtifactDownloader\Scope\Info\Factory;

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfo;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeInfoFactory implements ScopeInfoFactoryInterface
{
    /**
     * @param ScopeConfigInterface $config
     *
     * @return ScopeInfoInterface
     */
    public function makeForConfig(ScopeConfigInterface $config)
    {
        return new ScopeInfo($config);
    }
}

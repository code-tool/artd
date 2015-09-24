<?php

namespace CodeTool\ArtifactDownloader\Scope\Info\Factory;

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

interface ScopeInfoFactoryInterface
{
    /**
     * @param ScopeConfigInterface $config
     *
     * @return ScopeInfoInterface
     */
    public function makeForConfig(ScopeConfigInterface $config);
}

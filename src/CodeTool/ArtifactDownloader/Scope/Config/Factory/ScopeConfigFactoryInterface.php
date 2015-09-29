<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

interface ScopeConfigFactoryInterface
{
    /**
     * @param string                $scopeName
     * @param DomainObjectInterface $do
     *
     * @return ScopeConfigInterface
     */
    public function createFromDo($scopeName, DomainObjectInterface $do);
}

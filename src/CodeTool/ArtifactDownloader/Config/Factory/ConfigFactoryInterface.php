<?php

namespace CodeTool\ArtifactDownloader\Config\Factory;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

interface ConfigFactoryInterface
{
    /**
     * @param string                 $revision
     * @param ScopeConfigInterface[] $scopesConfigs
     *
     * @return ConfigInterface
     */
    public function create($revision, array $scopesConfigs);

    /**
     * @param string                $revision
     * @param DomainObjectInterface $do
     *
     * @return ConfigInterface
     */
    public function createFromDo($revision, DomainObjectInterface $do);

    /**
     * @param string $revision
     * @param array  $data
     *
     * @return ConfigInterface
     */
    public function createFromArray($revision, array $data);
}
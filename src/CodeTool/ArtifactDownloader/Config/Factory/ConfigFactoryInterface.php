<?php

namespace CodeTool\ArtifactDownloader\Config\Factory;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

interface ConfigFactoryInterface
{
    /**
     * @param string                 $version
     * @param string                 $timestamp
     * @param ScopeConfigInterface[] $scopesConfigs
     *
     * @return ConfigInterface
     */
    public function create($version, $timestamp, array $scopesConfigs);

    /**
     * @param DomainObjectInterface $do
     *
     * @return ConfigInterface
     */
    public function createFromDo(DomainObjectInterface $do);

    /**
     * @param array $data
     *
     * @return ConfigInterface
     */
    public function createFromArray(array $data);

    /**
     * @param string $json
     *
     * @return ConfigInterface
     */
    public function createFromJson($json);
}

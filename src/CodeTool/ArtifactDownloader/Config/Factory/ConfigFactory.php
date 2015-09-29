<?php

namespace CodeTool\ArtifactDownloader\Config\Factory;

use CodeTool\ArtifactDownloader\Config\Config;
use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\Factory\ScopeConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @var ScopeConfigFactoryInterface
     */
    private $scopeConfigFactory;

    /**
     * @param DomainObjectFactoryInterface $domainObjectFactory
     * @param ScopeConfigFactoryInterface  $scopeConfigFactory
     */
    public function __construct(
        DomainObjectFactoryInterface $domainObjectFactory,
        ScopeConfigFactoryInterface $scopeConfigFactory
    ) {
        $this->domainObjectFactory = $domainObjectFactory;
        $this->scopeConfigFactory = $scopeConfigFactory;
    }

    /**
     * @param string $version
     * @param string $timestamp
     * @param ScopeConfigInterface[] $scopesConfigs
     *
     * @return ConfigInterface
     */
    public function create($version, $timestamp, array $scopesConfigs)
    {
        return new Config($version, $timestamp, $scopesConfigs);
    }

    /**
     * @param DomainObjectInterface $do
     *
     * @return ConfigInterface
     */
    public function createFromDo(DomainObjectInterface $do)
    {
        $scopesConfig = $do->get('scope');
        $scopesConfigArray = [];
        foreach ($scopesConfig as $scopePath => $config) {
            $scopesConfigArray[] = $this->scopeConfigFactory->createFromDo($scopePath, $config);
        }

        return $this->create($do->get('version'), $do->get('timestamp'), $scopesConfigArray);
    }

    /**
     * @param array $data
     *
     * @return ConfigInterface
     */
    public function createFromArray(array $data)
    {
        return $this->createFromDo($this->domainObjectFactory->makeRecursiveFromArray($data));
    }

    /**
     * @param string $json
     *
     * @return ConfigInterface
     */
    public function createFromJson($json)
    {
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $this->createFromArray($data);
    }
}

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
     * @param string                 $revision
     * @param ScopeConfigInterface[] $scopesConfigs
     *
     * @return ConfigInterface
     */
    public function create($revision, array $scopesConfigs)
    {
        return new Config($revision, $scopesConfigs);
    }

    /**
     * @param string                $revision
     * @param DomainObjectInterface $do
     *
     * @return ConfigInterface
     */
    public function createFromDo($revision, DomainObjectInterface $do)
    {
        $scopesConfig = $do->get('scope');
        $scopesConfigArray = [];
        foreach ($scopesConfig as $scopePath => $config) {
            $scopesConfigArray[] = $this->scopeConfigFactory->createFromDo($scopePath, $config);
        }

        return $this->create($revision, $scopesConfigArray);
    }

    /**
     * @param string $revision
     * @param array  $data
     *
     * @return ConfigInterface
     */
    public function createFromArray($revision, array $data)
    {
        return $this->createFromDo($revision, $this->domainObjectFactory->makeRecursiveFromArray($data));
    }
}

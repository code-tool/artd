<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Factory;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderEtcd;
use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderFile;
use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactoryInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;

class ConfigProviderFactory implements ConfigProviderFactoryInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @var ConfigFactoryInterface
     */
    private $configFactory;

    /**
     * @var ConfigProviderResultFactoryInterface
     */
    private $configProviderResultFactory;

    /**
     * @var EtcdClientInterface
     */
    private $etcdClient;

    /**
     * @param ErrorFactoryInterface                $errorFactory
     * @param ConfigFactoryInterface               $configFactory
     * @param ConfigProviderResultFactoryInterface $configProviderResultFactory
     * @param EtcdClientInterface                  $etcdClient
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        ConfigFactoryInterface $configFactory,
        ConfigProviderResultFactoryInterface $configProviderResultFactory,
        EtcdClientInterface $etcdClient
    ) {
        $this->errorFactory = $errorFactory;
        $this->configFactory = $configFactory;
        $this->configProviderResultFactory = $configProviderResultFactory;

        $this->etcdClient = $etcdClient;
    }

    /**
     * @param string $path
     *
     * @return ConfigProviderInterface
     */
    public function makeEtcdProvider($path)
    {
        return new ConfigProviderEtcd(
            $this->errorFactory,
            $this->configFactory,
            $this->configProviderResultFactory,
            $this->etcdClient,
            $path
        );
    }

    /**
     * @param string $path
     *
     * @return ConfigProviderInterface
     */
    public function makeFileProvider($path)
    {
        return new ConfigProviderFile(
            $this->errorFactory,
            $this->configFactory,
            $this->configProviderResultFactory,
            $path
        );
    }

    /**
     * @param string $name
     * @param string $path
     *
     * @return ConfigProviderInterface
     */
    public function makeByProviderName($name, $path)
    {
        switch ($name) {
            case 'etcd':
                return $this->makeEtcdProvider($path);
            case 'file':
                return $this->makeFileProvider($path);
            default:
                throw new \InvalidArgumentException(sprintf('Unknown config provider with name "%s"', $name));
        }
    }
}

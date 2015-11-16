<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactoryInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;

class ConfigProviderEtcd extends ConfigProviderAbstract
{
    /**
     * @var EtcdClientInterface
     */
    private $etcdClient;

    /**
     * @var string
     */
    private $path;

    public function __construct(
        ErrorFactoryInterface $errorFactory,
        ConfigFactoryInterface $configFactory,
        ConfigProviderResultFactoryInterface $configProviderResultFactory,
        EtcdClientInterface $etcdClient,
        $path
    ) {
        parent::__construct($errorFactory, $configFactory, $configProviderResultFactory);

        $this->etcdClient = $etcdClient;
        $this->path = $path;
    }

    /**
     * @param int $revision
     *
     * @return Result\ConfigProviderResultInterface
     */
    public function getConfigAfterRevision($revision)
    {
        if (null === $revision) {
            $result = $this->etcdClient->get($this->path);
        } else {
            $result = $this->etcdClient->watch($this->path, $revision + 1);
        }

        if (false === $result->isSuccessful()) {
            return $this->getConfigProviderResultFactory()->createError($result->getError());
        }

        return $this->createResult(
            $result->getResponse()->getNode()->getModifiedIndex(),
            $result->getResponse()->getNode()->getValue()
        );
    }
}

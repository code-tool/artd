<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactoryInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;

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
     * @param EtcdClientResultInterface $etcdClientResult
     *
     * @return Result\ConfigProviderResultInterface
     */
    private function etcdClientResultToProviderResult(EtcdClientResultInterface $etcdClientResult)
    {
        if (false === $etcdClientResult->isSuccessful()) {
            return $this->getConfigProviderResultFactory()->createError($etcdClientResult->getError());
        }

        return $this->createResult(
            $etcdClientResult->getResponse()->getNode()->getModifiedIndex(),
            $etcdClientResult->getResponse()->getNode()->getValue()
        );
    }

    /**
     * @param int $revision
     *
     * @return Result\ConfigProviderResultInterface
     */
    public function getConfigAfterRevision($revision)
    {
        if (null === $revision) {
            $etcdClientResult = $this->etcdClient->get($this->path);
        } else {
            $etcdClientResult = $this->etcdClient->watch($this->path, $revision + 1);
        }

        if (false === $etcdClientResult->isSuccessful() &&
            null !== $etcdClientResult->getError()->getDetails() &&
            EtcdClientInterface::ERROR_CODE_EVENT_INDEX_CLEARED ===
                $etcdClientResult->getError()->getDetails()->getErrorCode()
        ) {
            $etcdClientResult = $this->etcdClient->get($this->path);
        }

        return $this->etcdClientResultToProviderResult($etcdClientResult);
    }
}

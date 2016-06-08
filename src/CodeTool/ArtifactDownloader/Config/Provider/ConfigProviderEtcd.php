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

    /**
     * @param ErrorFactoryInterface                $errorFactory
     * @param ConfigFactoryInterface               $configFactory
     * @param ConfigProviderResultFactoryInterface $configProviderResultFactory
     * @param EtcdClientInterface                  $etcdClient
     * @param string                               $path
     */
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
     * @param EtcdClientResultInterface $result
     *
     * @return bool
     */
    private function isResultIndexClearedError(EtcdClientResultInterface $result)
    {
        return
            false === $result->isSuccessful() &&
            null !== $result->getError()->getDetails() &&
            EtcdClientInterface::ERROR_CODE_EVENT_INDEX_CLEARED === $result->getError()->getDetails()->getErrorCode();
    }

    /**
     * @param int $revision
     *
     * @return Result\ConfigProviderResultInterface
     */
    public function getConfigAfterRevision($revision)
    {
        if (null === $revision) {
            return $this->etcdClientResultToProviderResult($this->etcdClient->get($this->path));
        }

        do {
            $etcdClientResult = $this->etcdClient->watch($this->path, $revision + 1);

            if ($etcdClientResult->isSuccessful() || false === $this->isResultIndexClearedError($etcdClientResult)) {
                return $this->etcdClientResultToProviderResult($etcdClientResult);
            }

            // current error is IndexClearedError
            $xEtcdIndex = $etcdClientResult->getError()->getDetails()->getIndex();

            $etcdClientResult = $this->etcdClient->get($this->path);
            if (false === $etcdClientResult->isSuccessful() ||
                $revision !== $etcdClientResult->getResponse()->getNode()->getModifiedIndex()
            ) {
                // if query failed or new response has higher modifiedIndex -> return immediately
                return $this->etcdClientResultToProviderResult($etcdClientResult);
            }

            $revision = $xEtcdIndex;
        } while (true);

        return $this->etcdClientResultToProviderResult($etcdClientResult);
    }
}

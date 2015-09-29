<?php

namespace CodeTool\ArtifactDownloader;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;
use CodeTool\ArtifactDownloader\Scope\State\ScopeStateBuilder;
use CodeTool\ArtifactDownloader\UnitConfig\UnitConfigInterface;
use CodeTool\ArtifactDownloader\UnitStatusBuilder\UnitStatusBuilderInterface;
use Psr\Log\LoggerInterface;

class ArtifactDownloader
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UnitConfigInterface
     */
    private $unitConfig;

    /**
     * @var ConfigFactoryInterface
     */
    private $configFactory;

    /**
     * @var EtcdClientInterface
     */
    private $etcdClient;

    /**
     * @var UnitStatusBuilderInterface
     */
    private $unitStatusBuilder;

    /**
     * @var ScopeStateBuilder
     */
    private $scopeStateBuilder;

    /**
     * @var int|null
     */
    private $lastConfigModifiedIndex;

    public function __construct(
        LoggerInterface $logger,
        UnitConfigInterface $unitConfig,
        EtcdClientInterface $etcdClient,
        ConfigFactoryInterface $configFactory,
        ScopeStateBuilder $scopeStateBuilder,
        UnitStatusBuilderInterface $unitStatusBuilder
    ) {
        $this->logger = $logger;
        $this->unitConfig = $unitConfig;
        $this->etcdClient = $etcdClient;
        $this->configFactory = $configFactory;
        $this->scopeStateBuilder = $scopeStateBuilder;
        $this->unitStatusBuilder = $unitStatusBuilder;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    private function logErrorAndPushToUnitStatus($message)
    {
        $this->logger->error($message);
        $this->unitStatusBuilder->addError($message);

        return $this;
    }

    /**
     * @return bool
     */
    private function updateUnitStatus()
    {
        $newUnitStatus = $this->unitStatusBuilder->build();
        $statusKey = $this->unitConfig->getStatusDirectoryPath() . '/' . $this->unitConfig->getName();

        $this->logger->debug(sprintf('Try to update unit status to %s', $newUnitStatus));

        $result = $this->etcdClient->set($statusKey, $newUnitStatus);

        if (null !== $result->getError()) {
            $this->logger->error(sprintf(
                'Failed to update unit status: %s',
                $result->getError()->getMessage()
            ));

            return false;
        }

        $this->logger->info(sprintf('Successfully updated unit status to %s', $newUnitStatus));

        return true;
    }

    private function handleEtcdClientResult(EtcdClientResultInterface $etcdClientResult)
    {
        if (null !== $etcdClientResult->getError()) {
            $this
                ->logErrorAndPushToUnitStatus(sprintf(
                    'Fail while getting new config revision: %s',
                    $etcdClientResult->getError()->getMessage()
                ))
                ->updateUnitStatus();

            return;
        }

        // Set new status
        $this->unitStatusBuilder->setStatus('applying');
        if (false === $this->updateUnitStatus()) {
            return;
        }

        // Get config revision
        $this->logger->debug(sprintf('New configModifiedIndex: %d', $this->lastConfigModifiedIndex));

        // Parse config and build collection
        $configString = $etcdClientResult->getResponse()->getNode()->getValue();
        $this->logger->debug(sprintf('Got new config: %s', $configString));

        // Parse Config ->
        $parsedConfig = $this->configFactory->createFromJson($configString);

        // BuildCollection
        $commandCollection = $this->scopeStateBuilder->buildForScopes($parsedConfig->getScopesConfig());
        $this->logger->debug(sprintf('Built command collection: %s%s', PHP_EOL, $commandCollection));

        // Apply command collection
        $configApplyResult = $commandCollection->execute();
        if (null !== $configApplyResult->getError()) {
            $this
                ->logErrorAndPushToUnitStatus($configApplyResult->getError()->getMessage())
                ->updateUnitStatus();

            return;
        }

        // Update applied config index
        $this->lastConfigModifiedIndex = $etcdClientResult->getResponse()->getNode()->getModifiedIndex();

        // $this-> UpdateVersion UnitScopeConfigVersiosn
        $this->unitStatusBuilder->setConfigVersion($parsedConfig->getVersion());
        $this->updateUnitStatus();
    }

    public function work()
    {
        // update unit status
        $this->updateUnitStatus();

        while (true) {
            if (null === $this->lastConfigModifiedIndex) {
                $this->logger->debug(sprintf(
                    'Try to get last config revision (%s).',
                    $this->unitConfig->getConfigPath()
                ));
                $result = $this->etcdClient->get($this->unitConfig->getConfigPath());
            } else {
                $this->logger->debug('Waiting for new config revision.');
                $result = $this->etcdClient->watch(
                    $this->unitConfig->getConfigPath(),
                    $this->lastConfigModifiedIndex + 1
                );
            }

            $this->handleEtcdClientResult($result);
        }
    }
}

<?php

namespace CodeTool\ArtifactDownloader;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\Processor\ScopeConfigProcessor;
use CodeTool\ArtifactDownloader\UnitConfig\UnitConfigInterface;
use CodeTool\ArtifactDownloader\UnitStatusBuilder\UnitStatusBuilderInterface;
use Psr\Log\LoggerInterface;

class ArtifactDownloader
{
    const VERSION = '@package_version@';

    const RELEASE_DATE = '@release_date@';

    const MAX_SLEEP_TIMEOUT = 60;

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
     * @var ScopeConfigProcessor
     */
    private $scopeConfigProcessor;

    /**
     * @var int|null
     */
    private $lastConfigModifiedIndex;

    /**
     * @var int
     */
    private $lastSleepTimeout = 0;

    /**
     * @param LoggerInterface            $logger
     * @param UnitConfigInterface        $unitConfig
     * @param EtcdClientInterface        $etcdClient
     * @param ConfigFactoryInterface     $configFactory
     * @param ScopeConfigProcessor       $scopeConfigProcessor
     * @param UnitStatusBuilderInterface $unitStatusBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        UnitConfigInterface $unitConfig,
        EtcdClientInterface $etcdClient,
        ConfigFactoryInterface $configFactory,
        ScopeConfigProcessor $scopeConfigProcessor,
        UnitStatusBuilderInterface $unitStatusBuilder
    ) {
        $this->logger = $logger;
        $this->unitConfig = $unitConfig;
        $this->etcdClient = $etcdClient;
        $this->configFactory = $configFactory;
        $this->scopeConfigProcessor = $scopeConfigProcessor;
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
        $this->unitStatusBuilder
            ->setStatus('error')
            ->addError($message);

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

        $this->logger->info(sprintf('Successfully updated unit status => %s', $newUnitStatus));

        return true;
    }

    /**
     * @param EtcdClientResultInterface $etcdClientResult
     *
     * @return bool
     */
    private function handleEtcdClientResult(EtcdClientResultInterface $etcdClientResult)
    {
        if (null !== $etcdClientResult->getError()) {
            $this
                ->logErrorAndPushToUnitStatus(sprintf(
                    'Fail while getting new config revision: %s',
                    $etcdClientResult->getError()->getMessage()
                ))
                ->updateUnitStatus();

            return false;
        }

        // Set new status
        $this->unitStatusBuilder->setStatus('applying');
        if (false === $this->updateUnitStatus()) {
            return false;
        }

        // Parse config and build collection
        $configString = $etcdClientResult->getResponse()->getNode()->getValue();
        $this->logger->debug(sprintf('Got new config: %s', $configString));

        // Parse Config ->
        $parsedConfig = $this->configFactory->createFromJson($configString);

        // Apply config
        $applyStart = microtime(true);

        // $this->logger->debug(sprintf('Built command collection: %s%s', PHP_EOL, $commandCollection));
        $configApplyResult = $this->scopeConfigProcessor->process($parsedConfig->getScopesConfig());
        if (null !== $configApplyResult->getError()) {
            $this->logger->error(sprintf('Error while config apply: %s', $configApplyResult->getError()->getMessage()));

            $this
                ->logErrorAndPushToUnitStatus($configApplyResult->getError()->getMessage())
                ->updateUnitStatus();

            return false;
        }

        $this->logger->info(sprintf(
            'Scope synchronised to version %s in %f sec',
            $parsedConfig->getVersion(),
            microtime(true) - $applyStart
        ));

        // Update applied config index
        $this->lastConfigModifiedIndex = $etcdClientResult->getResponse()->getNode()->getModifiedIndex();
        $this->logger->debug(sprintf('Last configModifiedIndex: %d', $this->lastConfigModifiedIndex));

        // $this-> UpdateVersion UnitScopeConfigVersion
        $this->unitStatusBuilder->setStatus('sync')->setConfigVersion($parsedConfig->getVersion());
        $this->updateUnitStatus();

        return true;
    }

    /**
     * @param bool $isSuccess
     */
    private function sleepOnError($isSuccess)
    {
        if (true === $isSuccess) {
            $this->lastSleepTimeout = 0;

            return;
        }

        $this->lastSleepTimeout += 5;
        if ($this->lastSleepTimeout >= self::MAX_SLEEP_TIMEOUT) {
            $this->lastSleepTimeout = self::MAX_SLEEP_TIMEOUT;
        }

        $this->logger->warning(sprintf('Sleeping after error %s sec', $this->lastSleepTimeout));

        sleep($this->lastSleepTimeout);
    }

    public function work()
    {
        $this->logger->notice(sprintf('ArtifactDownloader v: %s (%s)', self::VERSION, self::RELEASE_DATE));

        // update unit status
        $this->updateUnitStatus();

        while (true) {
            if (null === $this->lastConfigModifiedIndex) {
                $this->logger->info(sprintf(
                    'Try to get last config revision (%s).',
                    $this->unitConfig->getConfigPath()
                ));
                $result = $this->etcdClient->get($this->unitConfig->getConfigPath());
            } else {
                $this->logger->info('Waiting for new config revision.');
                $result = $this->etcdClient->watch(
                    $this->unitConfig->getConfigPath(),
                    $this->lastConfigModifiedIndex + 1
                );
            }

            $this->sleepOnError($this->handleEtcdClientResult($result));
        }
    }
}

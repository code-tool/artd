<?php

namespace CodeTool\ArtifactDownloader;

use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\Processor\ScopeConfigProcessor;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\UnitStatusUpdaterInterface;
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
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var UnitStatusUpdaterInterface
     */
    private $unitStatusUpdater;

    /**
     * @var ScopeConfigProcessor
     */
    private $scopeConfigProcessor;

    /**
     * @var string|null
     */
    private $lastConfigRevision;

    /**
     * @var int
     */
    private $lastSleepTimeout = 0;

    /**
     * @param LoggerInterface            $logger
     * @param ConfigProviderInterface    $configProvider
     * @param ScopeConfigProcessor       $scopeConfigProcessor
     * @param UnitStatusUpdaterInterface $unitStatusUpdater
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigProviderInterface $configProvider,
        ScopeConfigProcessor $scopeConfigProcessor,
        UnitStatusUpdaterInterface $unitStatusUpdater
    ) {
        $this->logger = $logger;
        $this->configProvider = $configProvider;

        $this->scopeConfigProcessor = $scopeConfigProcessor;
        $this->unitStatusUpdater = $unitStatusUpdater;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    private function logErrorAndPushToUnitStatus($message)
    {
        $this->logger->error($message);
        $this->unitStatusUpdater
            ->setStatus('error')
            ->addError($message);

        return $this;
    }

    /**
     * @return bool
     */
    private function updateUnitStatus()
    {
        $result = $this->unitStatusUpdater->flush();

        if (null !== $result->getError()) {
            $this->logger->error(sprintf(
                'Failed to update unit status: %s',
                $result->getError()->getMessage()
            ));

            return false;
        }

        // $this->logger->info(sprintf('Successfully updated unit status => %s', $newUnitStatus));

        return true;
    }

    /**
     * @param ConfigProviderResultInterface $configProviderResult
     *
     * @return bool
     */
    private function handleEtcdClientResult(ConfigProviderResultInterface $configProviderResult)
    {
        if (null !== $configProviderResult->getError()) {
            $this
                ->logErrorAndPushToUnitStatus(sprintf(
                    'Fail while getting new config revision: %s',
                    $configProviderResult->getError()->getMessage()
                ))
                ->updateUnitStatus();

            return false;
        }

        // Set new status
        $this->unitStatusUpdater->setStatus('applying');
        if (false === $this->updateUnitStatus()) {
            return false;
        }

        // Version ?
        // $this->logger->debug(sprintf('Got new config: %s', $configString));

        // Apply config
        $applyStart = microtime(true);
        $configApplyResult = $this->scopeConfigProcessor
            ->process($configProviderResult->getConfig()->getScopesConfig());

        if (false === $configApplyResult->isSuccessful()) {
            $this->logger->error(sprintf('Error while config apply: %s', $configApplyResult->getError()->getMessage()));

            $this
                ->logErrorAndPushToUnitStatus($configApplyResult->getError()->getMessage())
                ->updateUnitStatus();

            return false;
        }

        // Update applied config index
        $this->lastConfigRevision = $configProviderResult->getConfig()->getRevision();

        $this->logger->info(sprintf(
            'Scope synchronised to version %s in %f sec',
            $configProviderResult->getConfig()->getRevision(),
            microtime(true) - $applyStart
        ));
        $this->unitStatusUpdater->setStatus('sync')->setConfigVersion($this->lastConfigRevision);
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

        $this->lastSleepTimeout += mt_rand(1, 5);
        if ($this->lastSleepTimeout >= self::MAX_SLEEP_TIMEOUT) {
            $this->lastSleepTimeout = self::MAX_SLEEP_TIMEOUT;
        }

        $this->logger->warning(sprintf('Sleeping after error %s sec', $this->lastSleepTimeout));

        sleep($this->lastSleepTimeout);
    }

    /**
     * @param bool $infinity
     *
     * @return int
     */
    public function work($infinity = false)
    {
        $this->logger->notice(sprintf('ArtifactDownloader v: %s (%s)', self::VERSION, self::RELEASE_DATE));

        // update unit status
        $this->updateUnitStatus();

        do {
            $result = $this->configProvider->getConfigAfterRevision($this->lastConfigRevision);
            $handleResult = $this->handleEtcdClientResult($result);
            if ($infinity) {
                $this->sleepOnError($handleResult);
            }

        } while ($infinity);

        if (false === $handleResult) {
            return 1;
        }

        return 0;
    }
}

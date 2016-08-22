<?php

namespace CodeTool\ArtifactDownloader;

use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\Processor\ScopeConfigProcessorInterface;
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
     * @var ScopeConfigProcessorInterface
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
     * @param LoggerInterface               $logger
     * @param ConfigProviderInterface       $configProvider
     * @param ScopeConfigProcessorInterface $scopeConfigProcessor
     * @param UnitStatusUpdaterInterface    $unitStatusUpdater
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigProviderInterface $configProvider,
        ScopeConfigProcessorInterface $scopeConfigProcessor,
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
            ->addError($message, time())
            ->flush();

        return $this;
    }

    /**
     * @param ConfigProviderResultInterface $configProviderResult
     *
     * @return bool
     */
    private function handleConfigProviderResult(ConfigProviderResultInterface $configProviderResult)
    {
        if (null !== $configProviderResult->getError()) {
            $this->logErrorAndPushToUnitStatus(sprintf(
                'Fail while getting new config revision: %s',
                $configProviderResult->getError()->getMessage()
            ));

            return false;
        }

        $configVersion = $configProviderResult->getConfig()->getVersion();
        $configRevision = $configProviderResult->getConfig()->getRevision();

        $this->logger->notice(sprintf('Got new config version: %s (rev: %s)', $configVersion, $configRevision));

        // Todo is this error critical?
        $this->unitStatusUpdater
            ->setStatus(sprintf('Applying config. v: %s, rev: %s', $configVersion, $configRevision))
            ->flush();

        // Apply config
        $applyStart = microtime(true);
        $configApplyResult = $this->scopeConfigProcessor->process($configProviderResult->getConfig());

        if (false === $configApplyResult->isSuccessful()) {
            $this->logErrorAndPushToUnitStatus(
                sprintf('Error while config apply: %s', $configApplyResult->getError()->getMessage())
            );

            return false;
        }

        // Update applied config index
        $this->lastConfigRevision = $configRevision;

        $this->logger->notice(sprintf(
            'Scope synchronised to version %s in %f sec',
            $configVersion,
            microtime(true) - $applyStart
        ));

        $this->unitStatusUpdater->setConfigVersion($configVersion)->flush();

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

        do {
            $this->unitStatusUpdater
                ->setStatus(sprintf(
                    'Waiting for config. Last rev: %s',
                    $this->lastConfigRevision === null ? 'null' : $this->lastConfigRevision
                ))
                ->flush();

            $result = $this->configProvider->getConfigAfterRevision($this->lastConfigRevision);
            $handleResult = $this->handleConfigProviderResult($result);
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

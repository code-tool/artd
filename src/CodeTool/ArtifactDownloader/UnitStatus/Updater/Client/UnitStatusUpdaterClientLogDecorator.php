<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client;

use Psr\Log\LoggerInterface;

class UnitStatusUpdaterClientLogDecorator implements UnitStatusUpdaterClientInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UnitStatusUpdaterClientInterface
     */
    private $unitStatusUpdaterClient;

    public function __construct(LoggerInterface $logger, UnitStatusUpdaterClientInterface $unitStatusUpdaterClient)
    {
        $this->logger = $logger;
        $this->unitStatusUpdaterClient = $unitStatusUpdaterClient;
    }

    public function update($statusString)
    {
        $this->logger->debug(sprintf('Try to update unit status to %s', $statusString));

        return $this->unitStatusUpdaterClient->update($statusString);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class UnitStatusUpdaterClientNone implements UnitStatusUpdaterClientInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * UnitStatusUpdaterClientNone constructor.
     *
     * @param ResultFactoryInterface $resultFactory
     */
    public function __construct(ResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param string $statusString
     *
     * @return ResultInterface
     */
    public function update($statusString)
    {
        return $this->resultFactory->createSuccessful();
    }
}

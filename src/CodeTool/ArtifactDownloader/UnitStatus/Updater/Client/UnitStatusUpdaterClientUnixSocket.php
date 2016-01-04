<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;

class UnitStatusUpdaterClientUnixSocket implements UnitStatusUpdaterClientInterface
{
    const MAX_TIMEOUT = 1;

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $socketPath;

    /**
     * @var int
     */
    private $maxTimeout;

    /**
     * UnitStatusUpdaterClientSocket constructor.
     *
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $socketPath
     * @param int                    $maxTimeout
     */
    public function __construct(ResultFactoryInterface $resultFactory, $socketPath, $maxTimeout = self::MAX_TIMEOUT)
    {
        $this->resultFactory = $resultFactory;
        $this->socketPath = $socketPath;
        $this->maxTimeout = $maxTimeout;
    }

    public function update($statusString)
    {
        $sHandle = @fsockopen(sprintf('unix://%s', $this->socketPath), -1, $errNo, $errStr, $this->maxTimeout);
        if (false === $sHandle) {
            return $this->resultFactory->createError($errStr);
        }

        if (false === socket_set_timeout($sHandle, $this->maxTimeout)) {
            return $this->resultFactory->createErrorFromGetLast('Can\'t set socket timeout: ');
        }

        if (false === fwrite($sHandle, $statusString)) {
            return $this->resultFactory->createErrorFromGetLast('Can\'t write status to socket: ');
        }

        fclose($sHandle);

        return $this->resultFactory->createSuccessful();
    }
}

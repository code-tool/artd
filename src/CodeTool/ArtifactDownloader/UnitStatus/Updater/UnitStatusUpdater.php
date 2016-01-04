<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater;

use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientInterface;

class UnitStatusUpdater implements UnitStatusUpdaterInterface
{
    const MAX_ERRORS_COUNT = 10;

    /**
     * @var UnitStatusUpdaterClientInterface
     */
    private $updaterClient;

    private $status = 'undefined';

    private $errors = [];

    private $configVersion = 'undefined';

    /**
     * UnitStatusUpdater constructor.
     *
     * @param UnitStatusUpdaterClientInterface $updaterClient
     */
    public function __construct(UnitStatusUpdaterClientInterface $updaterClient)
    {
        $this->updaterClient = $updaterClient;
    }

    /**
     * @param string $error
     * @param int    $ts
     *
     * @return UnitStatusUpdater
     */
    public function addError($error, $ts)
    {
        $this->errors[] = ['ts' => $ts, 'msg' => $error];

        if (count($this->errors) > self::MAX_ERRORS_COUNT) {
            array_shift($this->errors);
        }

        return $this;
    }

    /**
     * @param string $status
     *
     * @return UnitStatusUpdater
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $configVersion
     *
     * @return UnitStatusUpdater
     */
    public function setConfigVersion($configVersion)
    {
        $this->configVersion = $configVersion;

        return $this;
    }

    protected function build()
    {
        return json_encode(
            [
                'ts' => time(),
                'status' => $this->status,
                'last_errors' => array_reverse($this->errors),
                'config_version' => $this->configVersion
            ],
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @return ResultInterface
     */
    public function flush()
    {
        return $this->updaterClient->update($this->build());
    }
}

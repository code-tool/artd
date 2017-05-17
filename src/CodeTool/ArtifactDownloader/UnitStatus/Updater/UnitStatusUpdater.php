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
     * @return UnitStatusUpdaterInterface
     */
    public function clearErrors()
    {
        $this->errors = [];

        return $this;
    }

    /**
     * @param string $error
     * @param int    $ts
     *
     * @return UnitStatusUpdaterInterface
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
     * @return UnitStatusUpdaterInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $configVersion
     *
     * @return UnitStatusUpdaterInterface
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
                'config_version' => $this->configVersion,
                'last_errors' => array_reverse($this->errors),
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
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

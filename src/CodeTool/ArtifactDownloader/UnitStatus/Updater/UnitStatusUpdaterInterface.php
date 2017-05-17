<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface UnitStatusUpdaterInterface
{
    /**
     * @return UnitStatusUpdaterInterface
     */
    public function clearErrors();

    /**
     * @param string $error
     * @param int    $ts
     *
     * @return UnitStatusUpdaterInterface
     */
    public function addError($error, $ts);

    /**
     * @param string $status
     *
     * @return UnitStatusUpdaterInterface
     */
    public function setStatus($status);

    /**
     * @param string $configVersion
     *
     * @return UnitStatusUpdaterInterface
     */
    public function setConfigVersion($configVersion);

    /**
     * @return ResultInterface
     */
    public function flush();
}

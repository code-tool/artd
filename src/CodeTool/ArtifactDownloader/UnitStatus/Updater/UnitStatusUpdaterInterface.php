<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface UnitStatusUpdaterInterface
{
    /**
     * @param string $error
     *
     * @return UnitStatusUpdaterInterface
     */
    public function addError($error);

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

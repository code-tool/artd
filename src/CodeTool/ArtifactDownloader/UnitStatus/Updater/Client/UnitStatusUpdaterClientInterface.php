<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface UnitStatusUpdaterClientInterface
{
    /**
     * @param string $statusString
     *
     * @return ResultInterface
     */
    public function update($statusString);
}

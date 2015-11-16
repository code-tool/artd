<?php

namespace CodeTool\ArtifactDownloader\UnitSatus\Updater\Client;

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

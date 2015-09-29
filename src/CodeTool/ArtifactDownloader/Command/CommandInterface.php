<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface CommandInterface
{
    /**
     * @return ResultInterface
     */
    public function execute();
}

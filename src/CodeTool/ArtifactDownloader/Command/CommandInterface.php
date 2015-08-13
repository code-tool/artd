<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

interface CommandInterface
{
    /**
     * @return CommandResultInterface
     */
    public function execute();
}

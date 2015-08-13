<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResult;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

class CommandMoveFile implements CommandInterface
{
    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $targetPath;

    /**
     * @param string $sourcePath
     * @param string $targetPath
     */
    public function __construct($sourcePath, $targetPath)
    {
        $this->sourcePath = $sourcePath;
        $this->targetPath = $targetPath;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @rename($this->sourcePath, $this->targetPath)) {
            $lastError = error_get_last();

            return new CommandResult(
                sprintf('Can\' move "%s" to "%s". %s', $this->sourcePath, $this->targetPath, $lastError['message'])
            );
        }

        return new CommandResult(null);
    }
}

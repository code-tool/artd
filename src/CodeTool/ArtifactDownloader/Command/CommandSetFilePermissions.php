<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResult;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

class CommandSetFilePermissions implements CommandInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int|string
     */
    private $mode;

    /**
     * @param string $filePath
     * @param string $mode
     */
    public function __construct($filePath, $mode)
    {
        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @chmod($this->filePath, $this->mode)) {
            $lastError = error_get_last();

            return new CommandResult(
                sprintf('Can\' set permissions "%s" on "%s". %s', $this->mode, $this->filePath, $lastError['message'])
            );
        }

        return new CommandResult(null);
    }
}

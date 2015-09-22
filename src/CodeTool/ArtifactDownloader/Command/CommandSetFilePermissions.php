<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

/**
 * Class CommandSetFilePermissions
 *
 * @todo implement recursive
 * @package CodeTool\ArtifactDownloader\Command
 */
class CommandSetFilePermissions implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int|string
     */
    private $mode;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $filePath
     * @param string                        $mode
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $filePath, $mode)
    {
        $this->commandResultFactory = $commandResultFactory;
        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @chmod($this->filePath, $this->mode)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\' set permissions "%s" on "%s"', $this->mode, $this->filePath)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

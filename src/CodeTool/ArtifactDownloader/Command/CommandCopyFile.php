<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandCopyFile implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $targetPath;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $sourcePath
     * @param string                        $targetPath
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $sourcePath, $targetPath)
    {
        $this->commandResultFactory = $commandResultFactory;
        $this->sourcePath = $sourcePath;
        $this->targetPath = $targetPath;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @copy($this->sourcePath, $this->targetPath)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\'t copy "%s" to "%s"', $this->sourcePath, $this->targetPath)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

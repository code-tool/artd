<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandSymlink implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var string
     */
    private $target;

    /**
     * @var int|string
     */
    private $source;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $target
     * @param string                        $source
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $target, $source)
    {
        $this->commandResultFactory = $commandResultFactory;
        $this->target = $target;
        $this->source = $source;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @symlink($this->source, $this->target)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\' create symlink with name "%s" to "%s"', $this->source, $this->target)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandChown implements CommandInterface
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
     * @var string
     */
    private $user;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $target
     * @param string                        $user
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $target, $user)
    {
        $this->commandResultFactory = $commandResultFactory;

        $this->target = $target;
        $this->user = $user;
    }


    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @chown($this->target, $this->user)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\'t chown "%s" to "%s"', $this->target, $this->user)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

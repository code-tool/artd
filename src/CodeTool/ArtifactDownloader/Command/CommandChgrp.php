<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandChgrp implements CommandInterface
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
    private $group;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $target
     * @param string                        $group
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $target, $group)
    {
        $this->commandResultFactory = $commandResultFactory;

        $this->target = $target;
        $this->group = $group;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @chgrp($this->target, $this->group)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\'t chgrp "%s" to "%s"', $this->target, $this->group)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

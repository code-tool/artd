<?php

namespace CodeTool\ArtifactDownloader\Command\Collection;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandCollection implements CommandCollectionInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var CommandInterface[]
     */
    private $commands = [];

    public function __construct(CommandResultFactoryInterface $commandResultFactory)
    {
        $this->commandResultFactory = $commandResultFactory;
    }

    /**
     * @param CommandInterface $command
     *
     * @return CommandCollectionInterface
     */
    public function add(CommandInterface $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        foreach ($this->commands as $command) {
            $result = $command->execute();
            if (false === $result->isSuccessful()) {
                return $result;
            }
        }

        return $this->commandResultFactory->createSuccess();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->commands);
    }
}

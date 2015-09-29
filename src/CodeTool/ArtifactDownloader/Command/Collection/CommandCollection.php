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

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     */
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

    /**
     * @return string
     */
    public function __toString()
    {
        $result  = [];

        foreach ($this->commands as $command) {
            $result[] = sprintf("\t%s", $command);
        }

        return implode(PHP_EOL, $result);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command\Collection;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\Result\CommandResult;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

class CommandCollection implements CommandCollectionInterface
{
    /**
     * @var CommandInterface[]
     */
    private $commands = [];

    /**
     * @param CommandInterface $command
     *
     * @return CommandCollectionInterface
     */
    public function add(CommandInterface $command)
    {
        $this->commands[] = $command;
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

        return new CommandResult(null);
    }
}

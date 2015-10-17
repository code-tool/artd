<?php

namespace CodeTool\ArtifactDownloader\Command\Collection;

use CodeTool\ArtifactDownloader\Command\CommandInterface;

interface CommandCollectionInterface extends CommandInterface, \Countable
{
    /**
     * @param CommandInterface $command
     *
     * @return CommandCollectionInterface
     */
    public function add(CommandInterface $command);
}

<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 13.08.15
 * Time: 22:29
 */

namespace CodeTool\ArtifactDownloader\Command\Collection;


use CodeTool\ArtifactDownloader\Command\CommandInterface;

interface CommandCollectionInterface extends CommandInterface
{
    /**
     * @param CommandInterface $command
     *
     * @return CommandCollectionInterface
     */
    public function add(CommandInterface $command);
}

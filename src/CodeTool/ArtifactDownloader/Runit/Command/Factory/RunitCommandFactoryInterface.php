<?php
namespace CodeTool\ArtifactDownloader\Runit\Command\Factory;

use CodeTool\ArtifactDownloader\Command\CommandInterface;

interface RunitCommandFactoryInterface
{
    /**
     * @param string $serviceName
     * @param string $targetState
     * @param bool   $fatal
     *
     * @return CommandInterface
     */
    public function createSetServiceStateCommand($serviceName, $targetState, $fatal = true);
}

<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Command\Factory;

use CodeTool\ArtifactDownloader\FcgiClient\Command\FcgiRequestCommand;

interface FcgiCommandFactoryInterface
{
    /**
     * @param string   $socketPath
     * @param string[] $headers
     * @param string   $stdin
     *
     * @return FcgiRequestCommand
     */
    public function createFcgiRequestCommand($socketPath, array $headers, $stdin);
}

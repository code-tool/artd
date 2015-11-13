<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Command\Factory;

use CodeTool\ArtifactDownloader\FcgiClient\Command\FcgiCommandRequest;

interface FcgiCommandFactoryInterface
{
    /**
     * @param string   $socketPath
     * @param string[] $headers
     * @param string   $stdin
     *
     * @return FcgiCommandRequest
     */
    public function createFcgiRequestCommand($socketPath, array $headers, $stdin);
}

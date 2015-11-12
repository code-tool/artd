<?php

namespace CodeTool\ArtifactDownloader\FcgiClient;

use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResultInterface;

interface FcgiClientInterface
{
    /**
     * @param string   $socketPath
     * @param string[] $headers
     * @param string   $stdin
     *
     * @return FcgiClientResultInterface
     */
    public function makeRequest($socketPath, array $headers, $stdin);
}

<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Command\Factory;

use CodeTool\ArtifactDownloader\FcgiClient\Command\FcgiCommandRequest;
use CodeTool\ArtifactDownloader\FcgiClient\FcgiClientInterface;

class FcgiCommandFactory implements FcgiCommandFactoryInterface
{
    /**
     * @var FcgiClientInterface
     */
    private $fcgiClient;

    /**
     * @param FcgiClientInterface $fcgiClient
     */
    public function __construct(FcgiClientInterface $fcgiClient)
    {
        $this->fcgiClient = $fcgiClient;
    }

    /**
     * @param string   $socketPath
     * @param string[] $headers
     * @param string   $stdin
     *
     * @return FcgiCommandRequest
     */
    public function createFcgiRequestCommand($socketPath, array $headers, $stdin)
    {
        return new FcgiCommandRequest($this->fcgiClient, $socketPath, $headers, $stdin);
    }
}

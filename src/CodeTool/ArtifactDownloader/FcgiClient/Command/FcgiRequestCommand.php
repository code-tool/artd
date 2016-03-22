<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\FcgiClient\FcgiClientInterface;

class FcgiRequestCommand implements CommandInterface
{
    /**
     * @var FcgiClientInterface
     */
    private $fcgiClient;

    /**
     * @var string
     */
    private $socketPath;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var string
     */
    private $stdin;

    /**
     * @param FcgiClientInterface $fcgiClient
     * @param string              $socketPath
     * @param string[]            $headers
     * @param string              $stdin
     */
    public function __construct(FcgiClientInterface $fcgiClient, $socketPath, array $headers, $stdin)
    {
        $this->fcgiClient = $fcgiClient;

        $this->socketPath = $socketPath;
        $this->headers = $headers;
        $this->stdin = $stdin;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->fcgiClient->makeRequest($this->fcgiClient, $this->headers, $this->stdin);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf(
            'fcgi_request(%s, %s, %s)',
            $this->socketPath,
            json_encode($this->headers),
            $this->stdin
        );
    }
}

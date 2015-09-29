<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponseInterface;
use CodeTool\ArtifactDownloader\Result\Result;

class HttpClientResult extends Result implements HttpClientResultInterface
{
    /**
     * @var HttpClientResponseInterface
     */
    private $httpClientResponse;

    /**
     * @param HttpClientResponseInterface $httpClientResponse
     * @param ErrorInterface|null         $error
     */
    public function __construct(HttpClientResponseInterface $httpClientResponse = null, ErrorInterface $error = null)
    {
        parent::__construct($error);

        $this->httpClientResponse = $httpClientResponse;
    }

    /**
     * @return HttpClientResponseInterface
     */
    public function getResponse()
    {
        return $this->httpClientResponse;
    }
}

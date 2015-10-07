<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Factory;

use CodeTool\ArtifactDownloader\HttpClient\Response\Header\Factory\HttpClientResponseHeaderRepositoryFactory;
use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponse;

class HttpClientResponseFactory implements HttpClientResponseFactoryInterface
{
    private $httpClientResponseHeaderRepositoryFactory;

    public function __construct(HttpClientResponseHeaderRepositoryFactory $httpClientResponseHeaderRepositoryFactory)
    {
        $this->httpClientResponseHeaderRepositoryFactory = $httpClientResponseHeaderRepositoryFactory;
    }

    /**
     * @param int      $code
     * @param string[] $headers
     * @param string   $body
     *
     * @return HttpClientResponse
     */
    public function make($code, array $headers, $body)
    {
        return new HttpClientResponse(
            $code,
            $this->httpClientResponseHeaderRepositoryFactory->createFromArray($headers),
            $body
        );
    }
}

<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response;

use CodeTool\ArtifactDownloader\HttpClient\Response\Header\HttpClientResponseHeaderRepositoryInterface;

interface HttpClientResponseInterface
{
    /**
     * @return int
     */
    public function getCode();

    /**
     * @return HttpClientResponseHeaderRepositoryInterface
     */
    public function getHeaders();

    /**
     * @return string
     */
    public function getBody();
}

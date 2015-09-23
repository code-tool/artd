<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Factory;

use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponse;

class HttpClientResponseFactory implements HttpClientResponseFactoryInterface
{
    /**
     * @param int      $code
     * @param string[] $headers
     * @param string   $body
     *
     * @return HttpClientResponse
     */
    public function make($code, array $headers, $body)
    {
        return new HttpClientResponse($code, $headers, $body);
    }
}

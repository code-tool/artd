<?php

namespace CodeTool\ArtifactDownloader\HttpClient;

use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

interface HttpClientInterface
{
    const METHOD_GET = 'GET';

    const METHOD_PUT = 'PUT';

    const METHOD_POST = 'POST';

    const METHOD_DELETE = 'DELETE';

    /**
     * @param string       $uri
     * @param string       $method
     * @param string|array $body
     *
     * @return HttpClientResultInterface
     */
    public function makeRequest($uri, $method, $body);

    /**
     * @param string $url
     * @param string $target
     *
     * @return HttpClientResultInterface
     */
    public function downloadFile($url, $target);
}

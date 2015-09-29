<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponseInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

interface HttpClientResultFactoryInterface
{
    /**
     * @param HttpClientResponseInterface|null $httpClientResponse
     * @param ErrorInterface|null              $error
     *
     * @return HttpClientResultInterface
     */
    public function create(HttpClientResponseInterface $httpClientResponse = null, ErrorInterface $error = null);

    /**
     * @param int      $code
     * @param string[] $headers
     * @param string   $body
     *
     * @return HttpClientResultInterface
     */
    public function createSuccessful($code, array $headers, $body);

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return HttpClientResultInterface
     */
    public function createError($message, $context = null, ErrorInterface $prevError = null);
}

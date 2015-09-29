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
     * @param HttpClientResponseInterface $httpClientResponse
     *
     * @return HttpClientResultInterface
     */
    public function createSuccessful(HttpClientResponseInterface $httpClientResponse);

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return HttpClientResultInterface
     */
    public function createError($message, $context = null, ErrorInterface $prevError = null);

    /**
     * @param HttpClientResponseInterface $httpClientResponse
     * @param string                      $message
     * @param null                        $context
     * @param ErrorInterface|null         $prevError
     *
     * @return HttpClientResultInterface
     */
    public function createErrorWithResponse(
        HttpClientResponseInterface $httpClientResponse,
        $message,
        $context = null,
        ErrorInterface $prevError = null
    );
}

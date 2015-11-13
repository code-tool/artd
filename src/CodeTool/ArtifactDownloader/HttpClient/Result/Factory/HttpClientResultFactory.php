<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponseInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResult;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

class HttpClientResultFactory implements HttpClientResultFactoryInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @param ErrorFactoryInterface $errorFactory
     */
    public function __construct(ErrorFactoryInterface $errorFactory)
    {
        $this->errorFactory = $errorFactory;
    }

    /**
     * @param HttpClientResponseInterface|null $httpClientResponse
     * @param ErrorInterface|null              $error
     *
     * @return HttpClientResultInterface
     */
    public function create(HttpClientResponseInterface $httpClientResponse = null, ErrorInterface $error = null)
    {
        return new HttpClientResult($httpClientResponse, $error);
    }

    /**
     * @param HttpClientResponseInterface $httpClientResponse
     *
     * @return HttpClientResultInterface
     */
    public function createSuccessful(HttpClientResponseInterface $httpClientResponse)
    {
        return $this->create($httpClientResponse, null);
    }

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return HttpClientResultInterface
     */
    public function createError($message, $context = null, ErrorInterface $prevError = null)
    {
        return $this->create(null, $this->errorFactory->create($message, $context, $prevError));
    }

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
    ) {
        return $this->create(
            $httpClientResponse,
            $this->errorFactory->create($message, $context, $prevError)
        );
    }
}

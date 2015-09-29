<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Response\Factory\HttpClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponseInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResult;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

class HttpClientResultFactory implements HttpClientResultFactoryInterface
{
    /**
     * @var HttpClientResponseFactoryInterface
     */
    private $httpClientResponseFactory;

    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @param HttpClientResponseFactoryInterface $httpClientResponseFactory
     * @param ErrorFactoryInterface              $errorFactory
     */
    public function __construct(
        HttpClientResponseFactoryInterface $httpClientResponseFactory,
        ErrorFactoryInterface $errorFactory
    ) {
        $this->httpClientResponseFactory = $httpClientResponseFactory;
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
     * @param int      $code
     * @param string[] $headers
     * @param string   $body
     *
     * @return HttpClientResultInterface
     */
    public function createSuccessful($code, array $headers, $body)
    {
        return $this->create($this->httpClientResponseFactory->make($code, $headers, $body), null);
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
}

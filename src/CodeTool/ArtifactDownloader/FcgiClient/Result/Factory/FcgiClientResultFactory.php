<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResult;
use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResultInterface;

class FcgiClientResultFactory implements FcgiClientResultFactoryInterface
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
     * @param null                $response
     * @param ErrorInterface|null $error
     *
     * @return FcgiClientResultInterface
     */
    public function create($response = null, ErrorInterface $error = null)
    {
        return new FcgiClientResult($response, $error);
    }

    /**
     * @param string $response
     *
     * @return FcgiClientResultInterface
     */
    public function createSuccess($response)
    {
        return $this->create($response);
    }

    /**
     * @param \Exception $exception
     *
     * @return FcgiClientResultInterface
     */
    public function createErrorFromException(\Exception $exception)
    {
        return $this->create(null, $this->errorFactory->createFromException($exception));
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Result;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class ResultFactory implements ResultFactoryInterface
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

    public function create(ErrorInterface $error = null)
    {
        return new Result($error);
    }

    /**
     * @return ResultInterface
     */
    public function createSuccessful()
    {
        return $this->create(null);
    }

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ResultInterface
     */
    public function createError($message, $context = null, ErrorInterface $prevError = null)
    {
        return $this->create($this->errorFactory->create($message, $context, $prevError));
    }

    /**
     * @param string              $prefix
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ResultInterface
     */
    public function createErrorFromGetLast($prefix, $context = null, ErrorInterface $prevError = null)
    {
        return $this->create($this->errorFactory->createFromGetLast($prefix, $context, $prevError));
    }

    /**
     * @param \Exception          $exception
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return Result|ResultInterface
     */
    public function createErrorFromException(\Exception $exception, $context = null, ErrorInterface $prevError = null)
    {
        return $this->create($this->errorFactory->createFromException($exception, $context, $prevError));
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface ResultFactoryInterface
{
    /**
     * @param ErrorInterface $error
     *
     * @return ResultInterface
     */
    public function create(ErrorInterface $error = null);

    /**
     * @return ResultInterface
     */
    public function createSuccessful();

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ResultInterface
     */
    public function createError($message, $context = null, ErrorInterface $prevError = null);

    /**
     * @param string              $prefix
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ResultInterface
     */
    public function createErrorFromGetLast($prefix, $context = null, ErrorInterface $prevError = null);

    /**
     * @param \Exception          $exception
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ResultInterface
     */
    public function createErrorFromException(\Exception $exception, $context = null, ErrorInterface $prevError = null);
}

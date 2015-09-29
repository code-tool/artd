<?php

namespace CodeTool\ArtifactDownloader\Error\Factory;

use CodeTool\ArtifactDownloader\Error\Error;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;

class ErrorFactory implements ErrorFactoryInterface
{
    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function create($message, $context = null, ErrorInterface $prevError = null)
    {
        return new Error($message, $context, $prevError);
    }

    /**
     * @param string              $prefix
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function createFromGetLast($prefix, $context = null, ErrorInterface $prevError = null)
    {
        $lastError = error_get_last();

        return $this->create(sprintf('%s. %s', $prefix, $lastError['message']), $context, $prevError);
    }

    /**
     * @param \Exception          $exception
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function createFromException(\Exception $exception, $context = null, ErrorInterface $prevError = null)
    {
        return $this->create($exception->getMessage(), $context, $prevError);
    }
}

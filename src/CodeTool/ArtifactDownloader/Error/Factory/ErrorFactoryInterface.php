<?php

namespace CodeTool\ArtifactDownloader\Error\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;

interface ErrorFactoryInterface
{
    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function create($message, $context = null, ErrorInterface $prevError = null);

    /**
     * @param string              $prefix
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function createFromGetLast($prefix, $context = null, ErrorInterface $prevError = null);

    /**
     * @param \Exception          $exception
     * @param null                $context
     * @param ErrorInterface|null $prevError
     *
     * @return ErrorInterface
     */
    public function createFromException(\Exception $exception, $context = null, ErrorInterface $prevError = null);
}

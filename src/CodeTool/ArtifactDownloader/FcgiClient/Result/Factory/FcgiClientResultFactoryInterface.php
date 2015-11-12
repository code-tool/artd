<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResultInterface;

interface FcgiClientResultFactoryInterface
{
    /**
     * @param null                $response
     * @param ErrorInterface|null $error
     *
     * @return FcgiClientResultInterface
     */
    public function create($response = null, ErrorInterface $error = null);

    /**
     * @param string $response
     *
     * @return FcgiClientResultInterface
     */
    public function createSuccess($response);

    /**
     * @param \Exception $exception
     *
     * @return FcgiClientResultInterface
     */
    public function createErrorFromException(\Exception $exception);
}

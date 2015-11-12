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
    public function make($response = null, ErrorInterface $error = null);
}

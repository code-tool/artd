<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Result\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResult;
use CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResultInterface;

class FcgiClientResultFactory implements FcgiClientResultFactoryInterface
{
    /**
     * @param null                $response
     * @param ErrorInterface|null $error
     *
     * @return FcgiClientResultInterface
     */
    public function make($response = null, ErrorInterface $error = null)
    {
        return new FcgiClientResult($response, $error);
    }
}

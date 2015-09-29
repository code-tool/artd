<?php

namespace CodeTool\ArtifactDownloader\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;

interface ResultInterface
{
    public function isSuccess();

    /**
     * @return ErrorInterface|null
     */
    public function getError();
}

<?php

namespace CodeTool\ArtifactDownloader\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;

interface ResultInterface
{
    public function isSuccessful();

    /**
     * @return ErrorInterface|null
     */
    public function getError();
}

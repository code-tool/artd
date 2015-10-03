<?php

namespace CodeTool\ArtifactDownloader\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;

interface ResultInterface
{
    /**
     * @return bool
     */
    public function isSuccessful();

    /**
     * @return ErrorInterface|null
     */
    public function getError();
}

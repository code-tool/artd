<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Result;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface FcgiClientResultInterface extends ResultInterface
{
    /**
     * @return string|null
     */
    public function getResponse();
}

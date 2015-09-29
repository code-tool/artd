<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Result;

use CodeTool\ArtifactDownloader\HttpClient\Response\HttpClientResponseInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface HttpClientResultInterface extends ResultInterface
{
    /**
     * @return HttpClientResponseInterface|null
     */
    public function getResponse();
}

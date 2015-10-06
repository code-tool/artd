<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result;

use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientSingleNodeResponseInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface EtcdClientResultInterface extends ResultInterface
{
    /**
     * @return EtcdClientResponseInterface|EtcdClientSingleNodeResponseInterface|null
     */
    public function getResponse();
}

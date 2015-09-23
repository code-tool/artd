<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result;

use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientSingleNodeResponseInterface;

interface EtcdClientResultInterface
{
    /**
     * @return EtcdClientErrorInterface|null
     */
    public function getError();

    /**
     * @return EtcdClientResponseInterface|EtcdClientSingleNodeResponseInterface|null
     */
    public function getResponse();
}

<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result;

use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;

interface EtcdClientResultInterface
{
    /**
     * @return EtcdClientErrorInterface|null
     */
    public function getError();

    /**
     * @return EtcdClientResponseInterface|null
     */
    public function getResponse();
}

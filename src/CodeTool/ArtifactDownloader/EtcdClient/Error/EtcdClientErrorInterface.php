<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetailsInterface;

interface EtcdClientErrorInterface extends ErrorInterface
{
    /**
     * @return EtcdClientErrorDetailsInterface
     */
    public function getDetails();
}

<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNodeInterface;

interface EtcdClientSingleNodeResponseInterface extends EtcdClientResponseInterface
{
    /**
     * @return EtcdClientResponseNodeInterface
     */
    public function getNode();

    /**
     * @return EtcdClientResponseNodeInterface|null
     */
    public function getPrevNode();
}

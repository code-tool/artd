<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

interface EtcdClientResponseInterface
{
    /**
     * @return int
     */
    public function getXEtcdIndex();

    /**
     * @return string
     */
    public function getAction();
}

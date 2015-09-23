<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

interface EtcdClientResponseInterface
{
    /**
     * @return string
     */
    public function getAction();
}

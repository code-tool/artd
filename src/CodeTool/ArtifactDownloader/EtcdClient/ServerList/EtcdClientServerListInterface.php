<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\ServerList;

interface EtcdClientServerListInterface
{
    /**
     * @return string
     */
    public function current();

    /**
     * @return EtcdClientServerListInterface
     */
    public function switchToNext();
}

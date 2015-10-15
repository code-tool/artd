<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\ServerList\Factory;

use CodeTool\ArtifactDownloader\EtcdClient\ServerList\EtcdClientServerList;
use CodeTool\ArtifactDownloader\EtcdClient\ServerList\EtcdClientServerListInterface;

class EtcdClientServerListFactory
{
    /**
     * @param string $serversStr
     *
     * @return EtcdClientServerListInterface
     */
    public function makeFromString($serversStr)
    {
        $servers = [];
        foreach (explode(',', $serversStr) as $server) {
            $server[] = rtrim($server, '/');
        }

        return new EtcdClientServerList($servers);
    }
}

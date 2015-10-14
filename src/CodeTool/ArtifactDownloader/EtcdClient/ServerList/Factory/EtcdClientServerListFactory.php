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
        $servers = explode(',', $serversStr);

        return new EtcdClientServerList($servers);
    }
}

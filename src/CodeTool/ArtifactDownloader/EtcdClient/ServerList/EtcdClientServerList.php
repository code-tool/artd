<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\ServerList;

class EtcdClientServerList implements EtcdClientServerListInterface
{
    /**
     * @var string[]
     */
    private $servers;

    /**
     * @param string[] $servers
     */
    public function __construct(array $servers)
    {
        $this->servers = $servers;
        reset($this->servers);
    }

    /**
     * @return string
     */
    public function current()
    {
        return current($this->servers);
    }

    /**
     * @return EtcdClientServerListInterface
     */
    public function switchToNext()
    {
        if (false === next($this->servers)) {
            reset($this->servers);
        }

        return $this;
    }
}

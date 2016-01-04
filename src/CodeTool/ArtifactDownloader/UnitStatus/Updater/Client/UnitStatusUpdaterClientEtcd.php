<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client;

use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;

class UnitStatusUpdaterClientEtcd implements UnitStatusUpdaterClientInterface
{
    /**
     * @var EtcdClientInterface
     */
    private $etcdClient;

    /**
     * @var string
     */
    private $statusPath;

    /**
     * UnitStatusUpdaterClientEtcd constructor.
     *
     * @param EtcdClientInterface $etcdClient
     * @param string              $statusPath
     */
    public function __construct(EtcdClientInterface $etcdClient, $statusPath)
    {
        $this->etcdClient = $etcdClient;
        $this->statusPath = $statusPath;
    }

    /**
     * @param string $statusString
     *
     * @return \CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface
     */
    public function update($statusString)
    {
        return $this->etcdClient->set($this->statusPath, $statusString);
    }
}

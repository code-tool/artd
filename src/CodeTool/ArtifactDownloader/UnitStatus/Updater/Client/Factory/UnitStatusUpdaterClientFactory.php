<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\Factory;

use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientEtcd;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientInterface;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientNone;

class UnitStatusUpdaterClientFactory implements UnitStatusUpdaterClientFactoryInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var EtcdClientInterface
     */
    private $etcdClient;

    /**
     * UnitStatusUpdaterClientFactory constructor.
     *
     * @param ResultFactoryInterface $resultFactory
     * @param EtcdClientInterface    $etcdClient
     */
    public function __construct(ResultFactoryInterface $resultFactory, EtcdClientInterface $etcdClient)
    {
        $this->resultFactory = $resultFactory;
        $this->etcdClient = $etcdClient;
    }

    /**
     * @return UnitStatusUpdaterClientNone
     */
    protected function makeNoneClient()
    {
        return new UnitStatusUpdaterClientNone($this->resultFactory);
    }

    /**
     * @param string $statusPath
     *
     * @return UnitStatusUpdaterClientEtcd
     */
    protected function makeEtcdClient($statusPath)
    {
        return new UnitStatusUpdaterClientEtcd($this->etcdClient, $statusPath);
    }

    /**
     * @param string $name
     * @param string $path
     *
     * @return UnitStatusUpdaterClientInterface
     */
    public function makeByName($name, $path)
    {
        switch (strtolower($name)) {
            case 'none':
                return $this->makeNoneClient();
            case 'etcd':
                return $this->makeEtcdClient($path);
            default:
                throw new \InvalidArgumentException(sprintf('Unknown unit status updater with name "%s"', $name));
        }
    }
}

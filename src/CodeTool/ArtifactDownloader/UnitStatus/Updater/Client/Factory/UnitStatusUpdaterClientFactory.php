<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\Factory;

use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientEtcd;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientInterface;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientNone;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientUnixSocket;

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
     * @param string $socketPath
     *
     * @return UnitStatusUpdaterClientUnixSocket
     */
    protected function makeUnixSocketClient($socketPath)
    {
        return new UnitStatusUpdaterClientUnixSocket($this->resultFactory, $socketPath);
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
            case self::CLIENT_NONE:
                return $this->makeNoneClient();
            case self::CLIENT_ETCD:
                return $this->makeEtcdClient($path);
            case self::CLIENT_UNIX_SOCKET:
                return $this->makeUnixSocketClient($path);
            default:
                throw new \InvalidArgumentException(sprintf('Unknown unit status updater with name "%s"', $name));
        }
    }
}

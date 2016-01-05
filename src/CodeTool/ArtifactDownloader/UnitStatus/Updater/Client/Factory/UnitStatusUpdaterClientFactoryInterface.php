<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\Factory;

use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientInterface;

interface UnitStatusUpdaterClientFactoryInterface
{
    const CLIENT_NONE = 'none';

    const CLIENT_ETCD = 'etcd';

    const CLIENT_UNIX_SOCKET = 'unix-socket';

    /**
     * @param string $name
     * @param string $path
     *
     * @return UnitStatusUpdaterClientInterface
     */
    public function makeByName($name, $path);
}

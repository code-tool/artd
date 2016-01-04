<?php

namespace CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\Factory;

use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\UnitStatusUpdaterClientInterface;

interface UnitStatusUpdaterClientFactoryInterface
{
    /**
     * @param string $name
     * @param string $path
     *
     * @return UnitStatusUpdaterClientInterface
     */
    public function makeByName($name, $path);
}

<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Factory;

use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderEtcd;
use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderFile;
use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderInterface;

interface ConfigProviderFactoryInterface
{
    /**
     * @param string $path
     *
     * @return ConfigProviderEtcd
     */
    public function makeEtcdProvider($path);

    /**
     * @param string $path
     *
     * @return ConfigProviderFile
     */
    public function makeFileProvider($path);

    /**
     * @param string $name
     * @param string $path
     *
     * @return ConfigProviderInterface
     */
    public function makeByProviderName($name, $path);
}

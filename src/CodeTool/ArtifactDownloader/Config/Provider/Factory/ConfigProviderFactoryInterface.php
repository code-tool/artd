<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Factory;

use CodeTool\ArtifactDownloader\Config\Provider\ConfigProviderInterface;

interface ConfigProviderFactoryInterface
{
    /**
     * @param string $name
     * @param string $path
     *
     * @return ConfigProviderInterface
     */
    public function makeByProviderName($name, $path);
}

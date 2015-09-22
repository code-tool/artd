<?php

namespace CodeTool\ArtifactDownloader\Config\Factory;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;

interface ConfigFactoryInterface
{
    /**
     * @param string $json
     *
     * @return ConfigInterface
     */
    public function makeFromJson($json);
}

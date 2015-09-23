<?php

namespace CodeTool\ArtifactDownloader\Config;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getUnitName();

    /**
     * @return int
     */
    public function getStatusDirectoryPath();

    /**
     * @return string
     */
    public function getConfigPath();
}

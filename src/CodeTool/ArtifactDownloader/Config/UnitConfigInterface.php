<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

interface UnitConfigInterface
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

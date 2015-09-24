<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

interface UnitConfigInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getStatusDirectoryPath();

    /**
     * @return string
     */
    public function getConfigPath();
}

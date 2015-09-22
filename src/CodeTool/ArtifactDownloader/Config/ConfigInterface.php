<?php

namespace CodeTool\ArtifactDownloader\Config;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return int
     */
    public function getTimestamp();

    /**
     * @return mixed
     */
    public function getScopes();
}

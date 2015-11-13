<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResultInterface;

interface ConfigProviderInterface
{
    /**
     * @param int $revision
     *
     * @return ConfigProviderResultInterface
     */
    public function getConfigAfterRevision($revision);
}

<?php
namespace CodeTool\ArtifactDownloader\Config\Provider\Result;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface ConfigProviderResultInterface extends ResultInterface
{
    /**
     * @return ConfigInterface|null
     */
    public function getConfig();
}

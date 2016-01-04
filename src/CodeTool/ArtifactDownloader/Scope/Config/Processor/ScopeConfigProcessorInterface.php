<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;

interface ScopeConfigProcessorInterface
{
    /**
     * @param ConfigInterface $config
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function process(ConfigInterface $config);
}

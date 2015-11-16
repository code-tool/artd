<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Result;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Result\Result;

class ConfigProviderResult extends Result implements ConfigProviderResultInterface
{
    /**
     * @var ConfigInterface|null
     */
    private $config;

    /**
     * ConfigProviderResult constructor.
     *
     * @param ConfigInterface $config
     * @param ErrorInterface  $error
     */
    public function __construct(ConfigInterface $config = null, ErrorInterface $error = null)
    {
        $this->config = $config;

        parent::__construct($error);
    }

    /**
     * @return ConfigInterface|null
     */
    public function getConfig()
    {
        return $this->config;
    }
}

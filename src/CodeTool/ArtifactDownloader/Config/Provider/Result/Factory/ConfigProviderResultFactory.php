<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Result\Factory;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResult;
use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResultInterface;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;

class ConfigProviderResultFactory implements ConfigProviderResultFactoryInterface
{
    /**
     * @param ConfigInterface|null $config
     * @param ErrorInterface|null  $error
     *
     * @return ConfigProviderResultInterface
     */
    public function create(ConfigInterface $config = null, ErrorInterface $error = null)
    {
        return new ConfigProviderResult($config, $error);
    }

    /**
     * @param ConfigInterface $config
     *
     * @return ConfigProviderResultInterface
     */
    public function createSuccess(ConfigInterface $config)
    {
        return $this->create($config, null);
    }

    /**
     * @param ErrorInterface $error
     *
     * @return ConfigProviderResultInterface
     */
    public function createError(ErrorInterface $error)
    {
        return $this->create(null, $error);
    }
}

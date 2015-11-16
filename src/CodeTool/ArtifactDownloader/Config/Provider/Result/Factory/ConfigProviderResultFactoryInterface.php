<?php

namespace CodeTool\ArtifactDownloader\Config\Provider\Result\Factory;

use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\ConfigProviderResultInterface;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;

interface ConfigProviderResultFactoryInterface
{
    /**
     * @param ConfigInterface|null $config
     * @param ErrorInterface|null  $error
     *
     * @return ConfigProviderResultInterface
     */
    public function create(ConfigInterface $config = null, ErrorInterface $error = null);

    /**
     * @param ConfigInterface $config
     *
     * @return ConfigProviderResultInterface
     */
    public function createSuccess(ConfigInterface $config);

    /**
     * @param ErrorInterface $error
     *
     * @return ConfigProviderResultInterface
     */
    public function createError(ErrorInterface $error);
}

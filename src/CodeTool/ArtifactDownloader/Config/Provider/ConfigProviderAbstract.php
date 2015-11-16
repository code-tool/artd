<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactoryInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;

abstract class ConfigProviderAbstract implements ConfigProviderInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @var ConfigFactoryInterface
     */
    private $configFactory;

    /**
     * @var ConfigProviderResultFactoryInterface
     */
    private $configProviderResultFactory;

    /**
     * ConfigProviderAbstract constructor.
     *
     * @param ErrorFactoryInterface                $errorFactory
     * @param ConfigFactoryInterface               $configFactory
     * @param ConfigProviderResultFactoryInterface $configProviderResultFactory
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        ConfigFactoryInterface $configFactory,
        ConfigProviderResultFactoryInterface $configProviderResultFactory
    ) {
        $this->errorFactory = $errorFactory;
        $this->configFactory = $configFactory;
        $this->configProviderResultFactory = $configProviderResultFactory;
    }

    /**
     * @return ErrorFactoryInterface
     */
    protected function getErrorFactory()
    {
        return $this->errorFactory;
    }

    /**
     * @return ConfigFactoryInterface
     */
    protected function getConfigFactory()
    {
        return $this->configFactory;
    }

    /**
     * @return ConfigProviderResultFactoryInterface
     */
    protected function getConfigProviderResultFactory()
    {
        return $this->configProviderResultFactory;
    }

    /**
     * @param string $configRevision
     * @param string $configStr
     *
     * @return Result\ConfigProviderResultInterface
     */
    protected function createResult($configRevision, $configStr)
    {
        $data = json_decode($configStr, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $this->getConfigProviderResultFactory()->createError(
                $this->getErrorFactory()->create(sprintf('Error while parsing config: %s', json_last_error_msg()))
            );
        }

        return $this->getConfigProviderResultFactory()
            ->createSuccess($this->getConfigFactory()->createFromArray($configRevision, $data));
    }
}

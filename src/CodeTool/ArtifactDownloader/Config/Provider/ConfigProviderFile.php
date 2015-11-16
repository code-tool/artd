<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactoryInterface;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactoryInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;

class ConfigProviderFile extends ConfigProviderAbstract
{
    const DEFAULT_POLL_INTERVAL = 1;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int
     */
    private $pollInterval;

    /**
     * @param ErrorFactoryInterface                $errorFactory
     * @param ConfigFactoryInterface               $configFactory
     * @param ConfigProviderResultFactoryInterface $configProviderResultFactory
     * @param                                      $filePath
     * @param int                                  $pollInterval
     */
    public function __construct(
        ErrorFactoryInterface $errorFactory,
        ConfigFactoryInterface $configFactory,
        ConfigProviderResultFactoryInterface $configProviderResultFactory,
        $filePath,
        $pollInterval = self::DEFAULT_POLL_INTERVAL
    ) {
        parent::__construct($errorFactory, $configFactory, $configProviderResultFactory);

        $this->filePath = $filePath;
        $this->pollInterval = $pollInterval;
    }

    public function getConfigAfterRevision($revision)
    {
        do {
            if (false === ($content = file_get_contents($this->filePath))) {
                return $this->getConfigProviderResultFactory()->createError(
                    $this->getErrorFactory()->createFromGetLast('Can\'t read config file.')
                );
            }

            $fileRevision = hash('md5', $content, false);

            if ($fileRevision !== $revision) {
                break;
            }

            sleep($this->pollInterval);
        } while (true);

        return $this->createResult($fileRevision, $content);
    }
}

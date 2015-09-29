<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

class UnitConfig implements UnitConfigInterface
{
    private function getOptOrEnvOrDefault($optName, $env, $default)
    {
        $opt = getopt([], $optName . '::');
        if (false !== $opt && array_key_exists($optName, $opt)) {
            return $opt[$optName];
        }

        if (false !== ($envVal = getenv(sprintf('ENV_%s', $env)))) {
            return $envVal;
        }

        return $default;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getOptOrEnvOrDefault('name', 'NAME', gethostname());
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->getOptOrEnvOrDefault('config-path', 'CONFIG_PATH', 'artifact-downloader/config');
    }

    /**
     * @return string
     */
    public function getStatusDirectoryPath()
    {
        return $this->getOptOrEnvOrDefault('status-directory', 'STATUS_DIRECTORY', 'artifact-downloader/status');
    }
}

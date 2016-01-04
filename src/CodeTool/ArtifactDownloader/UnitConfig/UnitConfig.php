<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

use CodeTool\ArtifactDownloader\EtcdClient\EtcdClient;

class UnitConfig implements UnitConfigInterface
{
    private function getOptOrEnvOrDefault($optName, $env, $default)
    {
        $opt = getopt('', [$optName . '::']);
        if (false !== $opt && array_key_exists($optName, $opt)) {
            return $opt[$optName];
        }

        if ('' !== $env && false !== ($envVal = getenv($env))) {
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
    public function getConfigProvider()
    {
        return $this->getOptOrEnvOrDefault('config-provider', 'CONFIG_PROVIDER', 'etcd');
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

    /**
     * @return string
     */
    public function getResourceCredentialsConfigPath()
    {
        return $this->getOptOrEnvOrDefault('resource-credentials', 'RESOURCE_CREDENTIALS', null);
    }

    /**
     * @return string
     */
    public function getEtcdServerUrl()
    {
        return $this->getOptOrEnvOrDefault('etcd-server-url', 'ETCD_SERVER_URL', EtcdClient::DEFAULT_SERVER);
    }

    /**
     * @return string
     */
    public function getStatusUpdaterClient()
    {
        return $this->getOptOrEnvOrDefault('status-updater-client', 'STATUS_UPDATER_CLIENT', 'etcd');
    }

    /**
     * @return bool
     */
    public function getIsApplyOnceMode()
    {
        return array_key_exists('apply-once', getopt('', ['apply-once']));
    }
}

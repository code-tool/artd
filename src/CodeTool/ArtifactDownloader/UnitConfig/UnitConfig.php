<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

use CodeTool\ArtifactDownloader\EtcdClient\EtcdClient;
use CodeTool\ArtifactDownloader\UnitStatus\Updater\Client\Factory\UnitStatusUpdaterClientFactoryInterface;

class UnitConfig implements UnitConfigInterface
{
    /**
     * @param string $optName
     * @param string $env
     * @param mixed  $default
     *
     * @return string
     */
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
    public function getConfigProvider()
    {
        return $this->getOptOrEnvOrDefault('config-provider', 'ARTD_CONFIG_PROVIDER', 'etcd');
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->getOptOrEnvOrDefault('config-path', 'ARTD_CONFIG_PATH', 'artifact-downloader/config');
    }

    /**
     * @return string
     */
    public function getStatusUpdaterClient()
    {
        return $this->getOptOrEnvOrDefault('status-updater-client', 'ARTD_STATUS_UPDATER_CLIENT', 'etcd');
    }

    /**
     * @return string
     */
    public function getStatusUpdaterPath()
    {
        $default = 'artifact-downloader/status'; // //UnitStatusUpdaterClientFactoryInterface::CLIENT_ETCD:
        if (UnitStatusUpdaterClientFactoryInterface::CLIENT_UNIX_SOCKET === $this->getStatusUpdaterClient()) {
            $default = '/tmp/artd-status-updater.sock';
        }

        return $this->getOptOrEnvOrDefault('status-updater-path', 'ARTD_STATUS_UPDATER_PATH', $default);
    }

    /**
     * @return string
     */
    public function getResourceCredentialsConfigPath()
    {
        return $this->getOptOrEnvOrDefault('resource-credentials', 'ARTD_RESOURCE_CREDENTIALS', null);
    }

    /**
     * @return string
     */
    public function getEtcdServerUrl()
    {
        return $this->getOptOrEnvOrDefault('etcd-server-url', 'ARTD_ETCD_SERVER_URL', EtcdClient::DEFAULT_SERVER);
    }

    /**
     * @return bool
     */
    public function getIsApplyOnceMode()
    {
        return array_key_exists('apply-once', getopt('', ['apply-once']));
    }
}

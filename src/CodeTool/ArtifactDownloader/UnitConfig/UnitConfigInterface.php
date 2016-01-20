<?php

namespace CodeTool\ArtifactDownloader\UnitConfig;

interface UnitConfigInterface
{
    /**
     * @return string
     */
    public function getLogLevel();

    /**
     * @return string
     */
    public function getConfigProvider();

    /**
     * @return string
     */
    public function getConfigPath();


    /**
     * @return string
     */
    public function getStatusUpdaterClient();

    /**
     * @return int
     */
    public function getStatusUpdaterPath();

    /**
     * @return string
     */
    public function getResourceCredentialsConfigPath();

    /**
     * @return string
     */
    public function getEtcdServerUrl();

    /**
     * @return bool
     */
    public function getIsApplyOnceMode();
}

<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials;

interface ResourceCredentialsInterface
{
    /**
     * @return string
     */
    public function getScheme();

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return string
     */
    public function getPort();

    /**
     * @return string|null
     */
    public function getClientCertPath();

    /**
     * @return string|null
     */
    public function getClientCertPassword();
}

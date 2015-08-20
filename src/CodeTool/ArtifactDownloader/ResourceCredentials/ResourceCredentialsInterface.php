<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials;

interface ResourceCredentialsInterface
{
    /**
     * @return string|null
     */
    public function getClientCertPath();

    /**
     * @return string|null
     */
    public function getClientCertPassword();
}

<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials\Repository;

use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentialsInterface;

interface ResourceCredentialsRepositoryInterface
{
    /**
     * @param string $url
     *
     * @return ResourceCredentialsInterface|null
     */
    public function getCredentialsByResourcePath($url);
}

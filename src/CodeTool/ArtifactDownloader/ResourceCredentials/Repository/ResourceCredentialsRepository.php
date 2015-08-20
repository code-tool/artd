<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials\Repository;

use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentialsInterface;

class ResourceCredentialsRepository implements ResourceCredentialsRepositoryInterface
{
    private $domainClientCerts;

    public function __construct(array $domainClientCerts = [])
    {
        $this->domainClientCerts = $domainClientCerts;
    }

    /**
     * @param string $url
     *
     * @return ResourceCredentialsInterface|null
     */
    public function getCredentialsByResourcePath($url)
    {
        return null;
    }
}

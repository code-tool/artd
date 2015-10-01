<?php
namespace CodeTool\ArtifactDownloader\ResourceCredentials\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentialsInterface;

interface ResourceCredentialsFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return ResourceCredentialsInterface
     */
    public function createFromDo(DomainObjectInterface $do);
}

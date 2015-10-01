<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentials;
use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentialsInterface;

class ResourceCredentialsFactory implements ResourceCredentialsFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return ResourceCredentialsInterface
     */
    public function createFromDo(DomainObjectInterface $do)
    {
        return new ResourceCredentials(
            $do->getOrDefault('scheme', 'https'),
            $do->get('host'),
            $do->getOrDefault('port', '443'),
            $do->get('client_cert_path'),
            $do->getOrDefault('client_cert_password', '')
        );
    }
}

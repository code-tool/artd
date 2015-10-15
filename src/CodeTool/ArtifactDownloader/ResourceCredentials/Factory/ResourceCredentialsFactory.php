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
        $clientCertPassword = null;
        if (null !== $clientCertPath = $do->getOrDefault('client_cert_path')) {
            $clientCertPassword = $do->getOrDefault('client_cert_password', '');
        }

        return new ResourceCredentials(
            $do->getOrDefault('scheme', 'https'),
            $do->get('host'),
            $do->getOrDefault('port', '443'),
            $clientCertPath,
            $clientCertPassword,
            $do->getOrDefault('http_proxy', '')
        );
    }
}

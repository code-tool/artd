<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials\Repository\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Factory\ResourceCredentialsFactoryInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepository;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class ResourceCredentialsRepositoryFactory
{
    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @var ResourceCredentialsFactoryInterface
     */
    private $resourceCredentialsFactory;

    /**
     * @param DomainObjectFactoryInterface        $domainObjectFactory
     * @param ResourceCredentialsFactoryInterface $resourceCredentialsFactory
     */
    public function __construct(
        DomainObjectFactoryInterface $domainObjectFactory,
        ResourceCredentialsFactoryInterface $resourceCredentialsFactory
    ) {
        $this->domainObjectFactory = $domainObjectFactory;
        $this->resourceCredentialsFactory = $resourceCredentialsFactory;
    }

    /**
     * @param DomainObjectInterface $domainObject
     *
     * @return ResourceCredentialsRepositoryInterface
     */
    public function createFromDo(DomainObjectInterface $domainObject)
    {
        $credentials = [];

        foreach ($domainObject as $entry) {
            $credentials[] = $this->resourceCredentialsFactory->createFromDo($entry);
        }

        return new ResourceCredentialsRepository($credentials);
    }

    /**
     * @param string $json
     *
     * @return ResourceCredentialsRepositoryInterface
     */
    public function createFromJson($json)
    {
        $decodedJson = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $this->createFromDo($this->domainObjectFactory->makeRecursiveFromArray($decodedJson));
    }

    /**
     * @param string $filePath
     *
     * @return ResourceCredentialsRepositoryInterface
     */
    public function createFromFile($filePath)
    {
        if (false === is_readable($filePath)) {
            throw new \RuntimeException(sprintf('Can\'t open %s file', $filePath));
        }

        $content = file_get_contents($filePath);

        return $this->createFromJson($content);
    }
}

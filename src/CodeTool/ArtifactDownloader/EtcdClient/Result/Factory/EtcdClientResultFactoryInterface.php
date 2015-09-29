<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;

interface EtcdClientResultFactoryInterface
{
    /**
     * @param EtcdClientErrorInterface|null    $error
     * @param EtcdClientResponseInterface|null $response
     *
     * @return EtcdClientResultInterface
     */
    public function create(EtcdClientErrorInterface $error = null, EtcdClientResponseInterface $response = null);

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResultInterface
     */
    public function createFromDo(DomainObjectInterface $do);

    /**
     * @param array $data
     *
     * @return EtcdClientResultInterface
     */
    public function createFromArray(array $data);

    /**
     * @param string $json
     *
     * @return EtcdClientResultInterface
     */
    public function createFromJson($json);
}

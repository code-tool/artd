<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

interface EtcdClientResultFactoryInterface
{
    /**
     * @param ErrorInterface|null              $error
     * @param EtcdClientResponseInterface|null $response
     *
     * @return EtcdClientResultInterface
     */
    public function create(ErrorInterface $error = null, EtcdClientResponseInterface $response = null);

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

    /**
     * @param HttpClientResultInterface $result
     *
     * @return EtcdClientResultInterface
     */
    public function createFromHttpClientResult(HttpClientResultInterface $result);
}

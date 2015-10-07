<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

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
     * @param int                   $xEtcdIndex
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResultInterface
     */
    public function createFromDo($xEtcdIndex, DomainObjectInterface $do);

    /**
     * @param HttpClientResultInterface $result
     *
     * @return EtcdClientResultInterface
     */
    public function createFromHttpClientResult(HttpClientResultInterface $result);
}

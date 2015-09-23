<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Factory\EtcdClientErrorFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Factory\EtcdClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResult;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;

class EtcdClientResultFactory implements EtcdClientResultFactoryInterface
{
    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @var EtcdClientErrorFactoryInterface
     */
    private $etcdClientErrorFactory;

    /**
     * @var EtcdClientResponseFactoryInterface
     */
    private $etcdClientResponseFactory;

    public function __construct(
        DomainObjectFactoryInterface $domainObjectFactory,
        EtcdClientErrorFactoryInterface $etcdClientErrorFactory,
        EtcdClientResponseFactoryInterface $etcdClientResponseFactory
    ) {
        $this->domainObjectFactory = $domainObjectFactory;
        $this->etcdClientErrorFactory = $etcdClientErrorFactory;
        $this->etcdClientResponseFactory = $etcdClientResponseFactory;
    }

    /**
     * @param EtcdClientErrorInterface|null    $error
     * @param EtcdClientResponseInterface|null $response
     *
     * @return EtcdClientResultInterface
     */
    public function make(EtcdClientErrorInterface $error = null, EtcdClientResponseInterface $response = null)
    {
        return new EtcdClientResult($error, $response);
    }

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResultInterface
     */
    public function makeFromDo(DomainObjectInterface $do)
    {
        if ($do->has(EtcdClientErrorFactoryInterface::ERROR_CODE_F_NAME)) {
            return $this->make($this->etcdClientErrorFactory->makeFromDo($do), null);
        }

        return $this->make(null, $this->etcdClientResponseFactory->makeFromDo($do));
    }

    /**
     * @param array $data
     *
     * @return EtcdClientResultInterface
     */
    public function makeFromArray(array $data)
    {
        return $this->makeFromDo($this->domainObjectFactory->makeRecursiveFromArray($data));
    }

    /**
     * @param string $json
     *
     * @return EtcdClientResultInterface
     */
    public function makeFromJson($json)
    {
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $this->makeFromArray($data);
    }
}

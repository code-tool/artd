<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Factory\EtcdClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResult;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResultInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;

class EtcdClientResultFactory implements EtcdClientResultFactoryInterface
{
    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @var EtcdClientResponseFactoryInterface
     */
    private $etcdClientResponseFactory;

    public function __construct(
        ErrorFactoryInterface $errorFactory,
        DomainObjectFactoryInterface $domainObjectFactory,
        EtcdClientResponseFactoryInterface $etcdClientResponseFactory
    ) {
        $this->errorFactory = $errorFactory;
        $this->domainObjectFactory = $domainObjectFactory;
        $this->etcdClientResponseFactory = $etcdClientResponseFactory;
    }

    /**
     * @param ErrorInterface|null              $error
     * @param EtcdClientResponseInterface|null $response
     *
     * @return EtcdClientResultInterface
     */
    public function create(ErrorInterface $error = null, EtcdClientResponseInterface $response = null)
    {
        return new EtcdClientResult($error, $response);
    }

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResultInterface
     */
    public function createFromDo(DomainObjectInterface $do)
    {
        if ($do->has('errorCode')) {
            $errorMessage = sprintf(
                '%s (cause: %s, errorCode: %d, index: %s)',
                $do->get('cause'),
                $do->get('errorCode'),
                $do->get('index'),
                $do->get('message')
            );

            return $this->create($this->errorFactory->create($errorMessage), null);
        }

        return $this->create(null, $this->etcdClientResponseFactory->makeFromDo($do));
    }

    /**
     * @param array $data
     *
     * @return EtcdClientResultInterface
     */
    public function createFromArray(array $data)
    {
        return $this->createFromDo($this->domainObjectFactory->makeRecursiveFromArray($data));
    }

    /**
     * @param string $json
     *
     * @return EtcdClientResultInterface
     */
    public function createFromJson($json)
    {
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $this->create($this->errorFactory->create(json_last_error_msg()));
        }

        return $this->createFromArray($data);
    }

    /**
     * @param HttpClientResultInterface $result
     *
     * @return EtcdClientResultInterface
     */
    public function createFromHttpClientResult(HttpClientResultInterface $result)
    {
        if (false === $result->isSuccessful()) {
            return $this->create($result->getError());
        }

        return $this->createFromJson($result->getResponse()->getBody());
    }
}

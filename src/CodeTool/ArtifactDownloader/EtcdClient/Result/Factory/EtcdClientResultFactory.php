<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\Factory\EtcdClientErrorDetailsFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Factory\EtcdClientErrorFactory;
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
     * @var EtcdClientResponseFactoryInterface
     */
    private $etcdClientResponseFactory;

    /**
     * @var EtcdClientErrorFactory
     */
    private $etcdClientErrorFactory;

    /**
     * @var EtcdClientErrorDetailsFactoryInterface
     */
    private $etcdClientErrorDetailsFactory;

    public function __construct(
        DomainObjectFactoryInterface $domainObjectFactory,
        EtcdClientResponseFactoryInterface $etcdClientResponseFactory,
        EtcdClientErrorFactory $etcdClientErrorFactory,
        EtcdClientErrorDetailsFactoryInterface $etcdClientErrorDetailsFactory
    ) {
        $this->domainObjectFactory = $domainObjectFactory;
        //
        $this->etcdClientResponseFactory = $etcdClientResponseFactory;
        //
        $this->etcdClientErrorFactory = $etcdClientErrorFactory;
        $this->etcdClientErrorDetailsFactory = $etcdClientErrorDetailsFactory;
    }

    /**
     * @param EtcdClientErrorInterface|null    $error
     * @param EtcdClientResponseInterface|null $response
     *
     * @return EtcdClientResultInterface
     */
    public function create(EtcdClientErrorInterface $error = null, EtcdClientResponseInterface $response = null)
    {
        return new EtcdClientResult($error, $response);
    }

    /**
     * @param int                   $xEtcdIndex
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResultInterface
     */
    public function createFromDo($xEtcdIndex, DomainObjectInterface $do)
    {
        if ($do->has('errorCode')) {
            $errorDetails = $this->etcdClientErrorDetailsFactory->makeFromDo($do);

            return $this->create($this->etcdClientErrorFactory->create((string) $errorDetails, $errorDetails), null);
        }

        return $this->create(null, $this->etcdClientResponseFactory->makeFromDo($xEtcdIndex, $do));
    }

    /**
     * @param HttpClientResultInterface $result
     *
     * @return EtcdClientResultInterface
     */
    public function createFromHttpClientResult(HttpClientResultInterface $result)
    {
        // Http request level error
        if (false === $result->isSuccessful() && null === $result->getResponse()) {
            return $this->create(
                $this->etcdClientErrorFactory->create(
                    $result->getError()->getMessage(),
                    null,
                    null,
                    $result->getError()
                )
            );
        }

        $responseBody = json_decode($result->getResponse()->getBody(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $this->create($this->etcdClientErrorFactory->create(json_last_error_msg()));
        }

        $responseDo = $this->domainObjectFactory->makeRecursiveFromArray($responseBody);

        return $this->createFromDo((int)$result->getResponse()->getHeaders()->get('x-etcd-index'), $responseDo);
    }
}

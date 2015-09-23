<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result;


use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;

class EtcdClientResult implements EtcdClientResultInterface
{
    /**
     * @var EtcdClientErrorInterface
     */
    private $error;

    /**
     * @var EtcdClientResponseInterface
     */
    private $response;

    /**
     * @param EtcdClientErrorInterface|null    $error
     * @param EtcdClientResponseInterface|null $response
     */
    public function __construct(EtcdClientErrorInterface $error = null, EtcdClientResponseInterface $response = null)
    {
        $this->error = $error;
        $this->response = $response;
    }

    /**
     * @return EtcdClientErrorInterface|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return EtcdClientResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}

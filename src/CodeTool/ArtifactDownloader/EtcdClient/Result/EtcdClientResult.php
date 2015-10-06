<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\Result\Result;

class EtcdClientResult extends Result implements EtcdClientResultInterface
{
    /**
     * @var EtcdClientResponseInterface
     */
    private $response;

    /**
     * @param ErrorInterface|null              $error
     * @param EtcdClientResponseInterface|null $response
     */
    public function __construct(ErrorInterface $error = null, EtcdClientResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($error);
    }

    /**
     * @return EtcdClientResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}

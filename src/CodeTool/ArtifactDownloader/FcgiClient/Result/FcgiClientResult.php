<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\Result\Result;

class FcgiClientResult extends Result implements FcgiClientResultInterface
{
    /**
     * @var string|null
     */
    private $response;

    /**
     * @param string              $response
     * @param ErrorInterface|null $error
     */
    public function __construct($response = null, ErrorInterface $error = null)
    {
        parent::__construct($error);

        $this->response = $response;
    }

    /**
     * @return string|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}

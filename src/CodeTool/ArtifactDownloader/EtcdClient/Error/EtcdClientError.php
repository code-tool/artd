<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error;

use CodeTool\ArtifactDownloader\Error\Error;
use CodeTool\ArtifactDownloader\Error\ErrorInterface;

use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetailsInterface;

class EtcdClientError extends Error implements EtcdClientErrorInterface
{
    /**
     * @var EtcdClientErrorDetailsInterface|null
     */
    private $details;

    /**
     * @param string                               $message
     * @param EtcdClientErrorDetailsInterface|null $details
     * @param mixed                                $context
     * @param ErrorInterface|null                  $prevError
     */
    public function __construct(
        $message,
        EtcdClientErrorDetailsInterface $details = null,
        $context = null,
        ErrorInterface $prevError = null
    ) {
        $this->details = $details;
        parent::__construct($message, $context, $prevError);
    }

    /**
     * @return EtcdClientErrorDetailsInterface|null
     */
    public function getDetails()
    {
        return $this->details;
    }
}

<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error\Factory;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetailsInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientError;

class EtcdClientErrorFactory
{
    /**
     * @param string                               $message
     * @param EtcdClientErrorDetailsInterface|null $details
     * @param null                                 $context
     * @param ErrorInterface|null                  $prevError
     *
     * @return EtcdClientError
     */
    public function create(
        $message,
        EtcdClientErrorDetailsInterface $details = null,
        $context = null,
        ErrorInterface $prevError = null
    ) {
        return new EtcdClientError($message, $details, $context, $prevError);
    }
}

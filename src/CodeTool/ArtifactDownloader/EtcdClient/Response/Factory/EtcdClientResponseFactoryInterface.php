<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;

interface EtcdClientResponseFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResponseInterface
     */
    public function makeFromDo(DomainObjectInterface $do);
}

<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientSingleNodeResponseInterface;

interface EtcdClientResponseFactoryInterface
{
    /**
     * @param int                   $xEtcdIndex
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResponseInterface|EtcdClientSingleNodeResponseInterface
     */
    public function makeFromDo($xEtcdIndex, DomainObjectInterface $do);
}

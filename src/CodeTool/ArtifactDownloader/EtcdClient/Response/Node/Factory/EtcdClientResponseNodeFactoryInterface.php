<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Node\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNodeInterface;

interface EtcdClientResponseNodeFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResponseNodeInterface
     */
    public function makeFromDomainObject(DomainObjectInterface $do);

    /**
     * @param array $nodeData
     *
     * @return EtcdClientResponseNodeInterface
     */
    public function makeFromArray(array $nodeData);
}

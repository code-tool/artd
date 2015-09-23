<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Node\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNode;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNodeInterface;

class EtcdClientResponseNodeFactory implements EtcdClientResponseNodeFactoryInterface
{
    const KEY_F_NAME = 'key';

    const VALUE_F_NAME = 'value';

    const CREATED_INDEX_F_NAME = 'createdIndex';

    const MODIFIED_INDEX_F_NAME = 'modifiedIndex';

    const TTL_F_NAME = 'ttl';

    const EXPIRATION_F_NAME = 'expiration';

    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @param DomainObjectFactoryInterface $domainObjectFactory
     */
    public function __construct(DomainObjectFactoryInterface $domainObjectFactory)
    {
        $this->domainObjectFactory = $domainObjectFactory;
    }

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResponseNodeInterface
     */
    public function makeFromDomainObject(DomainObjectInterface $do)
    {
        return new EtcdClientResponseNode(
            $do->get(self::KEY_F_NAME),
            //
            $do->getOrDefault(self::VALUE_F_NAME, null),
            //
            (int) $do->get(self::MODIFIED_INDEX_F_NAME),
            (int) $do->get(self::CREATED_INDEX_F_NAME),
            //
            (int) $do->getOrDefault(self::TTL_F_NAME, null),
            $do->getOrDefault(self::EXPIRATION_F_NAME, null)
        );
    }

    /**
     * @param array $nodeData
     *
     * @return EtcdClientResponseNodeInterface
     */
    public function makeFromArray(array $nodeData)
    {
        return $this->makeFromDomainObject($this->domainObjectFactory->makeFromArray($nodeData));
    }
}

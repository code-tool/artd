<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactoryInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientResponseInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientSingleNodeResponse;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\Factory\EtcdClientResponseNodeFactoryInterface;

class EtcdClientResponseFactory implements EtcdClientResponseFactoryInterface
{
    /**
     * @var DomainObjectFactoryInterface
     */
    private $domainObjectFactory;

    /**
     * @var EtcdClientResponseNodeFactoryInterface
     */
    private $etcdClientResponseNodeFactory;

    /**
     * @param DomainObjectFactoryInterface           $domainObjectFactory
     * @param EtcdClientResponseNodeFactoryInterface $etcdClientResponseNodeFactory
     */
    public function __construct(
        DomainObjectFactoryInterface $domainObjectFactory,
        EtcdClientResponseNodeFactoryInterface $etcdClientResponseNodeFactory
    ) {
        $this->domainObjectFactory = $domainObjectFactory;
        $this->etcdClientResponseNodeFactory = $etcdClientResponseNodeFactory;
    }

    /**
     * @param int                   $xEtcdIndex
     * @param string                $action
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientSingleNodeResponse
     */
    private function makeSingleNodeResponse($xEtcdIndex, $action, DomainObjectInterface $do)
    {
        $node = $this->etcdClientResponseNodeFactory->makeFromDomainObject($do->get('node'));

        $prevNode = null;
        if ($do->has('prevNode')) {
            $prevNode = $this->etcdClientResponseNodeFactory->makeFromDomainObject($do->get('prevNode'));
        }

        return new EtcdClientSingleNodeResponse($xEtcdIndex, $action, $node, $prevNode);
    }

    /**
     * @param int                   $xEtcdIndex
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientResponseInterface
     */
    public function makeFromDo($xEtcdIndex, DomainObjectInterface $do)
    {
        return $this->makeSingleNodeResponse($xEtcdIndex, $do->get('action'), $do);
    }
}

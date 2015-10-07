<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNodeInterface;

class EtcdClientSingleNodeResponse extends EtcdClientResponse implements EtcdClientSingleNodeResponseInterface
{
    /**
     * @var EtcdClientResponseNodeInterface
     */
    private $node;

    /**
     * @var EtcdClientResponseNodeInterface|null
     */
    private $prevNode;

    /**
     * @param int                                  $xEtcdIndex
     * @param string                               $action
     * @param EtcdClientResponseNodeInterface      $node
     * @param EtcdClientResponseNodeInterface|null $prevNode
     */
    public function __construct(
        $xEtcdIndex,
        $action,
        EtcdClientResponseNodeInterface $node,
        EtcdClientResponseNodeInterface $prevNode = null
    ) {
        parent::__construct($xEtcdIndex, $action);

        $this->node = $node;
        $this->prevNode = $prevNode;
    }

    /**
     * @return EtcdClientResponseNodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return EtcdClientResponseNodeInterface|null
     */
    public function getPrevNode()
    {
        return $this->prevNode;
    }
}

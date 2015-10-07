<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

class EtcdClientResponse implements EtcdClientResponseInterface
{
    /**
     * @var int
     */
    private $xEtcdIndex;

    /**
     * @var string
     */
    private $action;

    /**
     * @param int    $xEtcdIndex
     * @param string $action
     */
    public function __construct($xEtcdIndex, $action)
    {
        $this->xEtcdIndex = $xEtcdIndex;
        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getXEtcdIndex()
    {
        return $this->xEtcdIndex;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}

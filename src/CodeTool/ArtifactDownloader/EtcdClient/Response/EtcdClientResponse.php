<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

class EtcdClientResponse implements EtcdClientResponseInterface
{
    /**
     * @var string
     */
    private $action;

    /**
     * @param string $action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}

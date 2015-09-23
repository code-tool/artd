<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Node;

interface EtcdClientResponseNodeInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return int
     */
    public function getModifiedIndex();

    /**
     * @return string
     */
    public function getCreatedIndex();

    /**
     * @return int|null
     */
    public function getTtl();

    /**
     * @return null|string
     */
    public function getExpiration();
}

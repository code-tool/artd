<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error;

interface EtcdClientErrorInterface
{
    /**
     * @return string
     */
    public function getCause();

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return int
     */
    public function getIndex();

    /**
     * @return string
     */
    public function getMessage();
}

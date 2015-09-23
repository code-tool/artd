<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response;

interface EtcdClientResponseInterface
{
    /**
     * @return int
     */
    public function getRaftIndex();

    /**
     * @return int
     */
    public function getRaftTerm();

    /**
     * @return int
     */
    public function getEtcdIndex();

    /**
     * @return string
     */
    public function getAction();
}

<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response;

interface HttpClientResponseInterface
{
    /**
     * @return int
     */
    public function getCode();

    /**
     * @return \string[]
     */
    public function getHeaders();

    /**
     * @return string
     */
    public function getBody();
}

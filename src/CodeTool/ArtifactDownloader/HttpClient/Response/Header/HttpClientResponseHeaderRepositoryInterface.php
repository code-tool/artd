<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Header;

interface HttpClientResponseHeaderRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return string
     */
    public function get($name, $default = null);
}

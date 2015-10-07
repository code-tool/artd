<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Header;

class HttpClientResponseHeaderNormalizer
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function normalizeName($name)
    {
        return strtolower(trim($name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function normalizeValue($name)
    {
        return trim($name);
    }
}

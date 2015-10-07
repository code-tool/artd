<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Header;

class HttpClientResponseHeaderRepository implements HttpClientResponseHeaderRepositoryInterface
{
    /**
     * @var HttpClientResponseHeaderNormalizer
     */
    private $headerNormalizer;

    /**
     * @var string[]
     */
    private $headers;

    public function __construct(HttpClientResponseHeaderNormalizer $headerNormalizer, array $headers)
    {
        $this->headerNormalizer = $headerNormalizer;
        $this->headers = $headers;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        $normalizedName = $this->headerNormalizer->normalizeName($name);

        return array_key_exists($normalizedName, $this->headers);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return string
     */
    public function get($name, $default = null)
    {
        $normalizedName = $this->headerNormalizer->normalizeName($name);

        if (false === array_key_exists($normalizedName, $this->headers)) {
            return $default;
        }

        return $this->headers[$name];
    }
}

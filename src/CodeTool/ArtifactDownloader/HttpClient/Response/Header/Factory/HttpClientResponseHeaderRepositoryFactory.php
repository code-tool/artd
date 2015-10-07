<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response\Header\Factory;

use CodeTool\ArtifactDownloader\HttpClient\Response\Header\HttpClientResponseHeaderNormalizer;
use CodeTool\ArtifactDownloader\HttpClient\Response\Header\HttpClientResponseHeaderRepository;

class HttpClientResponseHeaderRepositoryFactory
{
    /**
     * @var HttpClientResponseHeaderNormalizer
     */
    private $headerNormalizer;

    /**
     * @param HttpClientResponseHeaderNormalizer $headerNormalizer
     */
    public function __construct(HttpClientResponseHeaderNormalizer $headerNormalizer)
    {
        $this->headerNormalizer = $headerNormalizer;
    }

    /**
     * @param string[] $headers
     *
     * @return HttpClientResponseHeaderRepository
     */
    public function createFromArray(array $headers)
    {
        $normalizedHeaders = [];

        foreach ($headers as $header) {
            list($headerName, $headerValue) = explode(':', $header);

            $normalizedHeaders[$this->headerNormalizer->normalizeName($headerName)] =
                $this->headerNormalizer->normalizeValue($headerValue);
        }

        return new HttpClientResponseHeaderRepository($this->headerNormalizer, $normalizedHeaders);
    }
}

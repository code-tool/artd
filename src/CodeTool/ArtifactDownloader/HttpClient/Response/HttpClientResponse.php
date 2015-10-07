<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response;

use CodeTool\ArtifactDownloader\HttpClient\Response\Header\HttpClientResponseHeaderRepositoryInterface;

class HttpClientResponse implements HttpClientResponseInterface
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var HttpClientResponseHeaderRepositoryInterface
     */
    private $headers;

    /**
     * @var string
     */
    private $body;

    /**
     * @param int                                         $code
     * @param HttpClientResponseHeaderRepositoryInterface $headersRepository
     * @param string                                      $body
     */
    public function __construct($code, HttpClientResponseHeaderRepositoryInterface $headersRepository, $body)
    {
        $this->code = $code;
        $this->headers = $headersRepository;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return HttpClientResponseHeaderRepositoryInterface
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

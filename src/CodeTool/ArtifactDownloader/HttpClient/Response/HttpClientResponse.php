<?php

namespace CodeTool\ArtifactDownloader\HttpClient\Response;

class HttpClientResponse implements HttpClientResponseInterface
{
    /**
     * @var int
     */
    private $responseCode;

    /**
     * @var string[]
     */
    private $responseHeaders;

    /**
     * @var string
     */
    private $responseBody;

    /**
     * @param int      $responseCode
     * @param string[] $responseHeaders
     * @param string   $responseBody
     */
    public function __construct($responseCode, array $responseHeaders, $responseBody)
    {
        $this->responseCode = $responseCode;
        $this->responseHeaders = $responseHeaders;
        $this->responseBody = $responseBody;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->responseCode;
    }

    /**
     * @return \string[]
     */
    public function getHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->responseBody;
    }
}

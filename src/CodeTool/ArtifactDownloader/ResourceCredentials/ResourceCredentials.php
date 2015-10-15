<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials;

class ResourceCredentials implements ResourceCredentialsInterface
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string|null
     */
    private $clientCertPath;

    /**
     * @var string|null
     */
    private $clientCertPassword;

    /**
     * @var string|null
     */
    private $httpProxy;

    /**
     * @param string      $scheme
     * @param string      $host
     * @param string      $port
     * @param string|null $clientCertPath
     * @param string|null $clientCertPassword
     * @param string|null $httpProxy
     */
    public function __construct($scheme, $host, $port, $clientCertPath, $clientCertPassword, $httpProxy)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;

        $this->clientCertPath = $clientCertPath;
        $this->clientCertPassword = $clientCertPassword;

        $this->httpProxy = $httpProxy;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string|null
     */
    public function getClientCertPath()
    {
        return $this->clientCertPath;
    }

    /**
     * @return string|null
     */
    public function getClientCertPassword()
    {
        return $this->clientCertPassword;
    }

    /**
     * @return string|null
     */
    public function getHttpProxy()
    {
        return $this->httpProxy;
    }
}

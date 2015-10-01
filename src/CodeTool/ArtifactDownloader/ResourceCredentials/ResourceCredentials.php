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
     * @var string
     */
    private $clientCertPath;

    /**
     * @var string
     */
    private $clientCertPassword;

    /**
     * @param string $scheme
     * @param string $host
     * @param string $port
     * @param string $clientCertPath
     * @param string $clientCertPassword
     */
    public function __construct($scheme, $host, $port, $clientCertPath, $clientCertPassword)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;

        $this->clientCertPath = $clientCertPath;
        $this->clientCertPassword = $clientCertPassword;
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
}

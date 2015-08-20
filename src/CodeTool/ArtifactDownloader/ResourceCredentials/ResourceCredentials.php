<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials;

class ResourceCredentials implements ResourceCredentialsInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string  $path
     * @param string  $password
     */
    public function __construct($path, $password)
    {
        $this->path = $path;
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getClientCertPath()
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getClientCertPassword()
    {
        return $this->password;
    }
}

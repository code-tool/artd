<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error;

class EtcdClientError implements EtcdClientErrorInterface
{
    /**
     * @var string
     */
    private $cause;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $cause
     * @param int    $errorCode
     * @param int    $index
     * @param string $message
     */
    public function __construct($cause, $errorCode, $index, $message)
    {
        $this->cause = $cause;
        $this->errorCode = $errorCode;
        $this->index = $index;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}

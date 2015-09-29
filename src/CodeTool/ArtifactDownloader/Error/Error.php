<?php

namespace CodeTool\ArtifactDownloader\Error;

class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var mixed
     */
    private $context;

    /**
     * @var ErrorInterface|null
     */
    private $prevError;

    /**
     * @param string              $message
     * @param null                $context
     * @param ErrorInterface|null $prevError
     */
    public function __construct($message, $context = null, ErrorInterface $prevError = null)
    {
        $this->message = $message;
        $this->context = $context;
        $this->prevError = $prevError;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return ErrorInterface|null
     */
    public function getPrevError()
    {
        return $this->prevError;
    }
}

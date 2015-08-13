<?php

namespace CodeTool\ArtifactDownloader\Command\Result;

class CommandResult implements CommandResultInterface
{
    /**
     * @var string
     */
    private $error;

    /**
     * @param string $error
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return null === $this->error || '' === $this->error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}

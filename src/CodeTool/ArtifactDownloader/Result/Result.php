<?php

namespace CodeTool\ArtifactDownloader\Result;

use CodeTool\ArtifactDownloader\Error\ErrorInterface;

class Result implements ResultInterface
{
    /**
     * @var ErrorInterface
     */
    private $error;

    /**
     * @param ErrorInterface|null $error
     */
    public function __construct(ErrorInterface $error = null)
    {
        $this->error = $error;
    }

    /**
     * @return ErrorInterface|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return null === $this->getError();
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command\Result\Factory;

use CodeTool\ArtifactDownloader\Command\Result\CommandResult;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

class CommandResultFactory implements CommandResultFactoryInterface
{
    /**
     * @return CommandResultInterface
     */
    public function createSuccess()
    {
        return new CommandResult(null);
    }

    /**
     * @param string $error
     *
     * @return CommandResultInterface
     */
    public function createError($error)
    {
        return new CommandResult($error);
    }

    /**
     * @param string $prefix
     *
     * @return CommandResultInterface
     */
    public function createErrorFromGetLast($prefix)
    {
        $lastError = error_get_last();

        return $this->createError(sprintf('%s. %s', $prefix, $lastError['message']));
    }
}

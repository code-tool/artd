<?php

namespace CodeTool\ArtifactDownloader\Command\Result\Factory;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

interface CommandResultFactoryInterface
{
    /**
     * @return CommandResultInterface
     */
    public function createSuccess();

    /**
     * @param string $error
     *
     * @return CommandResultInterface
     */
    public function createError($error);

    /**
     * @param string $prefix
     *
     * @return CommandResultInterface
     */
    public function createErrorFromGetLast($prefix);
}

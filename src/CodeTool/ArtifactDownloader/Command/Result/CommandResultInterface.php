<?php

namespace CodeTool\ArtifactDownloader\Command\Result;

interface CommandResultInterface
{
    /**
     * @return bool
     */
    public function isSuccessful();

    /**
     * @return string
     */
    public function getError();
}

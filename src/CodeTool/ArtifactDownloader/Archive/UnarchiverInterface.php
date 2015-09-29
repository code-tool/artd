<?php

namespace CodeTool\ArtifactDownloader\Archive;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface UnarchiverInterface
{
    /**
     * @param string $source
     * @param string $target
     *
     * @return ResultInterface
     */
    public function unarchive($source, $target);
}

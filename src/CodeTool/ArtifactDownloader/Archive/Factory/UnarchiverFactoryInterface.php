<?php

namespace CodeTool\ArtifactDownloader\Archive\Factory;

use CodeTool\ArtifactDownloader\Archive\UnarchiverInterface;

interface UnarchiverFactoryInterface
{
    /**
     * @param string $archiveType
     *
     * @return UnarchiverInterface
     */
    public function create($archiveType);
}

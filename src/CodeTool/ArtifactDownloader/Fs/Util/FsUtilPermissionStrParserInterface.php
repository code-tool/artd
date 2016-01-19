<?php

namespace CodeTool\ArtifactDownloader\Fs\Util;

interface FsUtilPermissionStrParserInterface
{
    /**
     * @param string $permissions
     *
     * @return array
     */
    public function parse($permissions);
}

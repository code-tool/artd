<?php

namespace CodeTool\ArtifactDownloader\DirectoryComparator;

interface DirectoryComparatorInterface
{
    /**
     * @param string $source
     * @param string $target
     * @param string $strategy
     *
     * @return bool
     */
    public function isEqual($source, $target, $strategy);
}

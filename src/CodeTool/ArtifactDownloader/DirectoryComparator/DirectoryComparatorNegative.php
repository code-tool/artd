<?php

namespace CodeTool\ArtifactDownloader\DirectoryComparator;

class DirectoryComparatorNegative implements DirectoryComparatorInterface
{
    /**
     * @param string $source
     * @param string $target
     * @param string $strategy
     *
     * @return bool
     */
    public function isEqual($source, $target, $strategy)
    {
        return false;
    }
}

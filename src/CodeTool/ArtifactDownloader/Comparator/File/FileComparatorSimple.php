<?php

namespace CodeTool\ArtifactDownloader\Comparator\File;

class FileComparatorSimple implements FileComparatorInterface
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
        return hash_file('md5', $source) === hash_file('md5', $target);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Comparator\File;

use CodeTool\ArtifactDownloader\Comparator\AbstractComparatorSimple;

class FileComparatorSimple extends AbstractComparatorSimple implements FileComparatorInterface
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
        $sourceFileObject = new \SplFileInfo($source);
        $targetFileObject = new \SplFileInfo($target);

        if (false === $this->isFilesEqual($sourceFileObject, $targetFileObject)) {
            return false;
        }
    }
}

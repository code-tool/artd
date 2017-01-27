<?php

namespace CodeTool\ArtifactDownloader\Comparator;

class AbstractComparatorSimple
{
    /**
     * @param \SplFileInfo $fileInfo1
     * @param \SplFileInfo $fileInfo2
     *
     * @return bool
     */
    protected function isFilesEqual(\SplFileInfo $fileInfo1, \SplFileInfo $fileInfo2)
    {
        if (false === $fileInfo1->isReadable() || false === $fileInfo2->isReadable()) {
            return false;
        }

        if ($fileInfo1->getSize() !== $fileInfo2->getSize()) {
            return false;
        }

        return $fileInfo1->getMTime() === $fileInfo2->getMTime();
    }
}

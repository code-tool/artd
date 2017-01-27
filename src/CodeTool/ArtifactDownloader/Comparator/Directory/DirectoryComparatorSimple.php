<?php

namespace CodeTool\ArtifactDownloader\Comparator\Directory;

use CodeTool\ArtifactDownloader\Comparator\AbstractComparatorSimple;

class DirectoryComparatorSimple extends AbstractComparatorSimple implements DirectoryComparatorInterface
{
    /**
     * @param string $path
     *
     * @return \RecursiveIteratorIterator
     */
    private function getSourceIterator($path)
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \RecursiveDirectoryIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_FILEINFO
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $filePath
     *
     * @return string
     */
    private function getTargetPathBySourcePath($source, $target, $filePath)
    {
        return str_replace($source, $target, $filePath);
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $strategy
     *
     * @return bool
     */
    public function isEqual($source, $target, $strategy)
    {
        /** @var \SplFileInfo $sourceFileObject */
        foreach ($this->getSourceIterator($source) as $sourceFileObject) {
            if ($sourceFileObject->isDir()) {
                continue;
            }

            $targetFilePath = $this->getTargetPathBySourcePath($source, $target, $sourceFileObject->getPathname());
            $targetFileObject = new \SplFileInfo($targetFilePath);

            if (false === $this->isFilesEqual($sourceFileObject, $targetFileObject)) {
                return false;
            }
        }

        return true;
    }
}

<?php

namespace CodeTool\ArtifactDownloader\DirectoryComparator;

class DirectoryComparatorSimple implements DirectoryComparatorInterface
{
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

    private function getTargetPathBySourcePath($source, $target, $filePath)
    {
        return str_replace($source, $target, $filePath);
    }

    private function isFilesEqual(\SplFileInfo $fileInfo1, \SplFileInfo $fileInfo2)
    {
        if ($fileInfo1->getSize() !== $fileInfo2->getSize()) {
            return false;
        }

        return $fileInfo1->getMTime() === $fileInfo2->getMTime();
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

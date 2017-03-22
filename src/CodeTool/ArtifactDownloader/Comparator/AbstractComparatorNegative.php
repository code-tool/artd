<?php

namespace CodeTool\ArtifactDownloader\Comparator;

abstract class AbstractComparatorNegative implements ComparatorInterface
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

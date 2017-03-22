<?php

namespace CodeTool\ArtifactDownloader\Comparator;

interface ComparatorInterface
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

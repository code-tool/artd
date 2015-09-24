<?php

namespace CodeTool\ArtifactDownloader\Scope\Info;

interface ScopeInfoInterface
{
    /**
     * @param string $target
     *
     * @return string
     */
    public function getAbsPathByForTarget($target);

    /**
     * @param string $target
     *
     * @return bool
     */
    public function isTargetExists($target);
}

<?php

namespace CodeTool\ArtifactDownloader\Scope\Info;

interface ScopeInfoInterface
{
    /**
     * @return string
     */
    public function getPath();

    /**
     * @return bool
     */
    public function isScopeExists();

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
    public function isPathForTargetIsAbs($target);

    /**
     * @param string $target
     *
     * @return bool
     */
    public function isTargetExists($target);
}

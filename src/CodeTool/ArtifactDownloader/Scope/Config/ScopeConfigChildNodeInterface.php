<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

interface ScopeConfigChildNodeInterface
{
    public function getRelativePath();

    /**
     * @return string [file / directory / symlink]
     */
    public function getType();

    /**
     * @return string
     */
    public function getSourcePath();

    /**
     * @return string
     */
    public function getSourceHash();

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @return string
     */
    public function getPermissions();
}

<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

interface ScopeConfigInterface
{
    /**
     * @return string
     */
    public function getScopePath();

    /**
     * @return bool
     */
    public function isExactMatchRequired();

    /**
     * @return ScopeConfigChildNodeInterface[]
     */
    public function getChildNodes();
}

<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

interface ScopeConfigInterface
{
    /**
     * @return string
     */
    public function getScopePath();

    /**
     * @return ScopeConfigChildNodeInterface[]
     */
    public function getChildNodes();
}

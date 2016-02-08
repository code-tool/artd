<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

interface ScopeConfigInterface
{
    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string|null
     */
    public function getCleanupPrefix();

    /**
     * @return ScopeConfigRuleInterface[]
     */
    public function getRules();
}

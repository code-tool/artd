<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

class ScopeConfig implements ScopeConfigInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $cleanupPrefix;

    /**
     * @var ScopeConfigRuleInterface[]
     */
    private $rules;

    /**
     * @param string                     $path
     * @param ScopeConfigRuleInterface[] $rules
     * @param string|null                $cleanupPrefix
     */
    public function __construct($path, array $rules, $cleanupPrefix = null)
    {
        $this->path = $path;
        $this->cleanupPrefix = $cleanupPrefix;
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return ScopeConfigRuleInterface[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return string|null
     */
    public function getCleanupPrefix()
    {
        return $this->cleanupPrefix;
    }
}

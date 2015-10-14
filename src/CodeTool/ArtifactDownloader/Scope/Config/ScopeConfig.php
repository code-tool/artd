<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

class ScopeConfig implements ScopeConfigInterface
{
    /**
     * @var string
     */
    private $scopePath;

    /**
     * @var ScopeConfigRuleInterface[]
     */
    private $scopeRules;

    /**
     * @param string                     $scopePath
     * @param bool                       $exactMatchRequired
     * @param ScopeConfigRuleInterface[] $scopeRules
     */
    public function __construct($scopePath, $exactMatchRequired, array $scopeRules)
    {
        $this->scopePath = $scopePath;
        $this->exactMatchRequired = $exactMatchRequired;
        $this->scopeRules = $scopeRules;
    }

    /**
     * @return string
     */
    public function getScopePath()
    {
        return $this->scopePath;
    }

    /**
     * @return bool
     */
    public function isExactMatchRequired()
    {
        return $this->exactMatchRequired;
    }

    /**
     * @return ScopeConfigRuleInterface[]
     */
    public function getScopeRules()
    {
        return $this->scopeRules;
    }
}

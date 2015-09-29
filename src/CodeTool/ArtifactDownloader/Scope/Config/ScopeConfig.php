<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

class ScopeConfig implements ScopeConfigInterface
{
    /**
     * @var string
     */
    private $scopePath;

    /**
     * @var ScopeConfigChildNodeInterface[]
     */
    private $childNodes;

    /**
     * @param string                          $scopePath
     * @param bool                            $exactMatchRequired
     * @param ScopeConfigChildNodeInterface[] $childNodes
     */
    public function __construct($scopePath, $exactMatchRequired, array $childNodes)
    {
        $this->scopePath = $scopePath;
        $this->exactMatchRequired = $exactMatchRequired;
        $this->childNodes = $childNodes;
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
     * @return ScopeConfigChildNodeInterface[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }
}

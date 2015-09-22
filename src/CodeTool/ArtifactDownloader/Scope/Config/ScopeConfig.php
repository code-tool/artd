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
     * @param ScopeConfigChildNodeInterface[] $childNodes
     */
    public function __construct($scopePath, array $childNodes)
    {
        $this->scopePath = $scopePath;
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
     * @return ScopeConfigChildNodeInterface[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }
}

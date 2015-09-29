<?php

namespace CodeTool\ArtifactDownloader\Scope\Info;

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class ScopeInfo implements ScopeInfoInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getAbsPathByForTarget($target)
    {
        return $this->scopeConfig->getScopePath() . DIRECTORY_SEPARATOR . $target;
    }

    public function isTargetExists($target)
    {
        return file_exists($this->getAbsPathByForTarget($target));
    }
}

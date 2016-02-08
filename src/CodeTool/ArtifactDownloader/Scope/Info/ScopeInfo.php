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

    /**
     * @return bool
     */
    public function isScopeExists()
    {
        return file_exists($this->scopeConfig->getPath());
    }

    /**
     * @param string $target
     *
     * @return string
     */
    public function getAbsPathByForTarget($target)
    {
        return $this->scopeConfig->getPath() . DIRECTORY_SEPARATOR . ltrim($target, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $target
     *
     * @return bool
     */
    public function isTargetExists($target)
    {
        return file_exists($this->getAbsPathByForTarget($target));
    }
}

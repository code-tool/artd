<?php
namespace CodeTool\ArtifactDownloader\Config;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getTimestamp();

    /**
     * @return \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    public function getScopesConfig();
}

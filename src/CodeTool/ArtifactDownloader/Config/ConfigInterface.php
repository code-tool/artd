<?php
namespace CodeTool\ArtifactDownloader\Config;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getRevision();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface[]
     */
    public function getScopesConfig();
}

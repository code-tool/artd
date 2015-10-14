<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;

interface ScopeConfigRuleInterface extends DomainObjectInterface
{
    /**
     * @return string [file / directory / symlink]
     */
    public function getType();
}

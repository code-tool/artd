<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;

interface ScopeConfigChildNodeInterface extends DomainObjectInterface
{
    /**
     * @return string [file / directory / symlink]
     */
    public function getType();
}

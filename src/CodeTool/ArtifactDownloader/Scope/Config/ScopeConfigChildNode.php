<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

use CodeTool\ArtifactDownloader\DomainObject\DomainObject;

class ScopeConfigChildNode extends DomainObject implements ScopeConfigChildNodeInterface
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }
}

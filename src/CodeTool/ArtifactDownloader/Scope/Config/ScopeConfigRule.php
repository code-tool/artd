<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

use CodeTool\ArtifactDownloader\DomainObject\DomainObject;

class ScopeConfigRule extends DomainObject implements ScopeConfigRuleInterface
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }
}

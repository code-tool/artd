<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfig;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRule;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class ScopeConfigFactory implements ScopeConfigFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return ScopeConfigRule
     */
    private function createChildNode(DomainObjectInterface $do)
    {
        return new ScopeConfigRule($do->toArray());
    }

    /**
     * @param string                 $scopeName
     * @param DomainObjectInterface $do
     *
     * @return ScopeConfigInterface
     */
    public function createFromDo($scopeName, DomainObjectInterface $do)
    {
        $scopeRules = [];
        foreach ($do->get('rules') as $rule) {
            $scopeRules[] = $this->createChildNode($rule);
        }

        return new ScopeConfig($scopeName, $do->getOrDefault('exact_match', false), $scopeRules);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfig;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNode;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;

class ScopeConfigFactory implements ScopeConfigFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return ScopeConfigChildNode
     */
    private function createChildNode(DomainObjectInterface $do)
    {
        return new ScopeConfigChildNode($do->toArray());
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

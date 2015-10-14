<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

interface ScopeStateTypeHandlerInterface
{
    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return bool
     */
    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    );
}

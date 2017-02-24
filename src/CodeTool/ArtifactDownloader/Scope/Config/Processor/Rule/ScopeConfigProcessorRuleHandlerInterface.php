<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

interface ScopeConfigProcessorRuleHandlerInterface
{
    const CONFIG_RULE_GROUP = 'group';
    const CONFIG_RULE_OWNER = 'owner';
    const CONFIG_RULE_MODE = 'mode';
    const CONFIG_RULE_PERMISSIONS = 'permissions';
    const CONFIG_RULE_ARCHIVE_FORMAT = 'archive_format';
    const CONFIG_RULE_TARGET = 'target';
    const CONFIG_RULE_SOURCE = 'source';

    /**
     * @return string[]
     */
    public function getSupportedTypes();

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return ResultInterface
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    );
}

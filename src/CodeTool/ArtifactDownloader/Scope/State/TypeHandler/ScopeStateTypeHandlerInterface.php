<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

interface ScopeStateTypeHandlerInterface
{
    /**
     * @param CommandCollectionInterface    $collection
     * @param ScopeInfoInterface            $scopeInfo
     * @param ScopeConfigChildNodeInterface $scopeConfigChildNode
     *
     * @return bool
     */
    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    );
}

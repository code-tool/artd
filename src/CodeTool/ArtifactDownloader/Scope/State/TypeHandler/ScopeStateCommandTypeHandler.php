<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeStateCommandTypeHandler implements ScopeStateTypeHandlerInterface
{
    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    ) {
        /*
        if ($scopeConfigChildNode->getType() !== 'command') {
            return false;
        }*/

        return false;
    }
}
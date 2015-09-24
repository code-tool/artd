<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeStateSymlinkTypeHandler implements ScopeStateTypeHandlerInterface
{
    public function __construct(
        BasicUtil $basicUtil,
        CommandFactoryInterface $commandFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->commandFactory = $commandFactory;
    }

    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    ) {
        if ('symlink' !== $scopeConfigChildNode->getType()) {
            return false;
        }

        $collection->add($this->commandFactory->createSymlinkCommand(
            $scopeInfo->getAbsPathByForTarget($scopeConfigChildNode->get('target')),
            $scopeInfo->getAbsPathByForTarget($scopeConfigChildNode->get('source'))
        ));

        return true;
    }
}

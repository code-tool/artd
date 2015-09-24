<?php

namespace CodeTool\ArtifactDownloader\Scope\State;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Info\Factory\ScopeInfoFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Scope\State\TypeHandler\ScopeStateTypeHandlerInterface;

class ScopeStateBuilder
{
    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @var ScopeInfoFactoryInterface
     */
    private $scopeInfoFactory;

    /**
     * @var ScopeStateTypeHandlerInterface[]
     */
    private $typeHandlers;

    /**
     * @param CommandFactoryInterface          $commandFactory
     * @param ScopeInfoFactoryInterface        $scopeInfoFactory
     * @param ScopeStateTypeHandlerInterface[] $typeHandlers
     */
    public function __construct(
        CommandFactoryInterface $commandFactory,
        ScopeInfoFactoryInterface $scopeInfoFactory,
        array $typeHandlers
    ) {
        $this->commandFactory = $commandFactory;
        $this->typeHandlers = $typeHandlers;
    }

    /**
     * @param CommandCollectionInterface    $collection
     * @param ScopeInfoInterface            $scopeInfo
     * @param ScopeConfigChildNodeInterface $childNode
     *
     * @return bool
     */
    private function addForChildNode(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $childNode
    ) {
        foreach ($this->typeHandlers as $typeHandler) {
            if (true === $typeHandler->handle($collection, $scopeInfo, $childNode)) {
                return true;
            }
        }

        return false;
    }

    public function build(ScopeConfigInterface $scopeConfig)
    {
        $collection = $this->commandFactory->createCollection();
        $scopeInfo = $this->scopeInfoFactory->makeForConfig($scopeConfig);

        foreach ($scopeConfig->getChildNodes() as $childNode) {
            if (false === $this->addForChildNode($collection, $scopeInfo, $childNode)) {
                // todo implement error message and processing break
            }
        }

        if (true === $scopeConfig->isExactMatchRequired()) {
            // todo Implement
        }

        return $collection;
    }
}

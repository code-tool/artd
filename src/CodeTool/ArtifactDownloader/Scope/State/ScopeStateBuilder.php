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
        $this->scopeInfoFactory = $scopeInfoFactory;
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

    private function addCommands(CommandCollectionInterface $collection, ScopeConfigInterface $scopeConfig)
    {
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

    /**
     * @param ScopeConfigInterface[] $scopesConfig
     *
     * @return CommandCollectionInterface
     */
    public function buildForScopes(array $scopesConfig)
    {
        $collection = $this->commandFactory->createCollection();

        foreach ($scopesConfig as $scopeConfig) {
            $this->addCommands($collection, $scopeConfig);
        }

        return $collection;
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorRuleTypeFcgiRequestHandler implements ScopeConfigProcessorRuleTypeHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @param ResultFactoryInterface  $resultFactory
     * @param CommandFactoryInterface $commandFactory
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        CommandFactoryInterface $commandFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @return string[]
     */
    public function getSupportedTypes()
    {
        return ['fcgi-request'];
    }

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
    ) {
        $collection->add(
            $this->commandFactory->createFcgiRequestCommand(
                $scopeConfigRule->get('socket'),
                (array) $scopeConfigRule->getOrDefault('headers', []),
                $scopeConfigRule->get('stdin')
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

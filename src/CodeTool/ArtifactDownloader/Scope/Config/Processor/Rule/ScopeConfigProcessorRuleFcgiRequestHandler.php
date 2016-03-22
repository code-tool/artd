<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Command\Factory\FcgiCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorRuleFcgiRequestHandler implements ScopeConfigProcessorRuleHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var FcgiCommandFactoryInterface
     */
    private $fcgiCommandFactory;

    /**
     * @param ResultFactoryInterface      $resultFactory
     * @param FcgiCommandFactoryInterface $fcgiCommandFactory
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        FcgiCommandFactoryInterface $fcgiCommandFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->fcgiCommandFactory = $fcgiCommandFactory;
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
            $this->fcgiCommandFactory->createFcgiRequestCommand(
                $scopeConfigRule->get('socket'),
                (array)$scopeConfigRule->getOrDefault('headers', []),
                $scopeConfigRule->get('stdin')
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

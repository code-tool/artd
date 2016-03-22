<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Runit\Command\Factory\RunitCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorRuleRunitHandler implements ScopeConfigProcessorRuleHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var RunitCommandFactoryInterface
     */
    private $runitCommandFactory;

    /**
     * @param ResultFactoryInterface       $resultFactory
     * @param RunitCommandFactoryInterface $runitCommandFactory
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        RunitCommandFactoryInterface $runitCommandFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->runitCommandFactory = $runitCommandFactory;
    }

    /**
     * @inheritdoc
     */
    public function getSupportedTypes()
    {
        return ['runit'];
    }

    /**
     * @inheritdoc
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $fatal = filter_var(
            $scopeConfigRule->getOrDefault('fatal', true),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if (null === $fatal) {
            $fatal = true;
        }

        $collection->add($this->runitCommandFactory
            ->createSetServiceStateCommand($scopeConfigRule->get('name'), $scopeConfigRule->get('state'), $fatal));

        return $this->resultFactory->createSuccessful();
    }
}

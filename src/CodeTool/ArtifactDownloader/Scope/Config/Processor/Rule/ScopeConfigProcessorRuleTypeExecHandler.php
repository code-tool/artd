<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\CmdRunner\Command\Factory\CmdRunnerCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorRuleTypeExecHandler implements ScopeConfigProcessorRuleTypeHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CmdRunnerCommandFactoryInterface
     */
    private $cmdRunnerCommandFactory;

    /**
     * @param ResultFactoryInterface           $resultFactory
     * @param CmdRunnerCommandFactoryInterface $cmdRunnerCommandFactory
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        CmdRunnerCommandFactoryInterface $cmdRunnerCommandFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->cmdRunnerCommandFactory = $cmdRunnerCommandFactory;
    }

    /**
     * @inheritdoc
     */
    public function getSupportedTypes()
    {
        return ['exec'];
    }

    /**
     * @inheritdoc
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $clearEnv = filter_var(
            $scopeConfigRule->getOrDefault('clear_env', false),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if (null === $clearEnv) {
            $clearEnv = false;
        }

        $env = [];
        if ($scopeConfigRule->has('env')) {
            $env = $scopeConfigRule->getOrDefault('env')->toArray();
        }

        $collection->add($this->cmdRunnerCommandFactory->createExecCommand(
            $scopeConfigRule->get('cmd'),
            $scopeConfigRule->getOrDefault('cwd', $scopeInfo->getAbsPathByForTarget('')),
            $clearEnv,
            $env
        ));

        return $this->resultFactory->createSuccessful();
    }
}

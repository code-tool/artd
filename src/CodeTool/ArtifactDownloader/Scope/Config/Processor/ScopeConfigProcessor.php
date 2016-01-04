<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor;

use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Config\ConfigInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule\ScopeConfigProcessorRuleTypeHandlerInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\Factory\ScopeInfoFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use Psr\Log\LoggerInterface;

class ScopeConfigProcessor implements ScopeConfigProcessorInterface
{
    private $logger;

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @var ScopeInfoFactoryInterface
     */
    private $scopeInfoFactory;

    /**
     * @var Rule\ScopeConfigProcessorRuleTypeHandlerInterface[]
     */
    private $typeHandlers;

    /**
     * @param LoggerInterface                                $logger
     * @param ResultFactoryInterface                         $resultFactory
     * @param CommandFactoryInterface                        $commandFactory
     * @param                                                $scopeInfoFactory $scopeInfoFactory
     * @param ScopeConfigProcessorRuleTypeHandlerInterface[] $typeHandlers
     */
    public function __construct(
        LoggerInterface $logger,
        ResultFactoryInterface $resultFactory,
        CommandFactoryInterface $commandFactory,
        ScopeInfoFactoryInterface $scopeInfoFactory,
        array $typeHandlers
    ) {
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
        $this->commandFactory = $commandFactory;
        $this->scopeInfoFactory = $scopeInfoFactory;

        $this->typeHandlers = $typeHandlers;
    }

    /**
     * @param ScopeConfigRuleInterface $rule
     *
     * @return ScopeConfigProcessorRuleTypeHandlerInterface|null
     */
    private function getHandlerForRule(ScopeConfigRuleInterface $rule)
    {
        foreach ($this->typeHandlers as $typeHandler) {
            if (true === in_array($rule->getType(), $typeHandler->getSupportedTypes(), true)) {
                return $typeHandler;
            }
        }

        return null;
    }

    /**
     * @param ScopeInfoInterface       $scopeInfo
     * @param ScopeConfigRuleInterface $rule
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    private function handleRule(ScopeInfoInterface $scopeInfo, ScopeConfigRuleInterface $rule)
    {
        if (null === ($handler = $this->getHandlerForRule($rule))) {
            return $this->resultFactory->createError('Can\'t find handler for rule type %s', $rule->getType());
        }

        $collection = $this->commandFactory->createCollection();
        $buildResult = $handler->buildCollection($collection, $scopeInfo, $rule);

        if (false === $buildResult->isSuccessful()) {
            return $buildResult;
        }

        if ($collection->count() > 0) {
            $this->logger->debug(sprintf('Built collection for rule ->%s%s', PHP_EOL, $collection));
        }

        return $collection->execute();
    }

    /**
     * @param ScopeConfigInterface $scopeConfig
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    private function processScopeConfig(ScopeConfigInterface $scopeConfig)
    {
        $this->logger->info(sprintf('Applying config for scope %s', $scopeConfig->getScopePath()));

        $scopeInfo = $this->scopeInfoFactory->makeForConfig($scopeConfig);

        foreach ($scopeConfig->getScopeRules() as $rule) {
            $ruleHandleResult = $this->handleRule($scopeInfo, $rule);

            if (false === $ruleHandleResult->isSuccessful()) {
                return $ruleHandleResult;
            }

        }

        /*if (true === $scopeConfig->isExactMatchRequired()) {
            // todo Implement
        }*/

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @param ConfigInterface $config
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function process(ConfigInterface $config)
    {
        foreach ($config->getScopesConfig() as $scopeConfig) {
            $processResult = $this->processScopeConfig($scopeConfig);

            if (false === $processResult->isSuccessful()) {
                return $processResult;
            }
        }

        return $this->resultFactory->createSuccessful();
    }
}

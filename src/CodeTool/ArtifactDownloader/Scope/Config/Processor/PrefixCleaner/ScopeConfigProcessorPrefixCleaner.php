<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\PrefixCleaner;

use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\Factory\ScopeInfoFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorPrefixCleaner
{
    /**
     * @var ScopeInfoFactoryInterface
     */
    private $scopeInfoFactory;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @var FsCommandFactoryInterface
     */
    private $fsCommandFactory;

    /**
     * @param ScopeInfoFactoryInterface $scopeInfoFactory
     * @param CommandFactoryInterface   $commandFactory
     * @param FsCommandFactoryInterface $fsCommandFactory
     */
    public function __construct(
        ScopeInfoFactoryInterface $scopeInfoFactory,
        CommandFactoryInterface $commandFactory,
        FsCommandFactoryInterface $fsCommandFactory
    ) {
        $this->scopeInfoFactory = $scopeInfoFactory;
        $this->commandFactory = $commandFactory;
        $this->fsCommandFactory = $fsCommandFactory;
    }

    /**
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface[] $scopeRules
     *
     * @return string[]
     */
    private function buildScopePaths(ScopeInfoInterface $scopeInfo, array $scopeRules)
    {
        $result = [];
        foreach ($scopeRules as $rule) {
            if (false === $rule->has('target')) {
                continue;
            }

            $result[] = $scopeInfo->getAbsPathByForTarget($rule->get('target'));
        }

        return $result;
    }

    private function getNearestPathForPrefix(ScopeInfoInterface $scopeInfo, $prefix)
    {
        $path = $scopeInfo->getAbsPathByForTarget($prefix);

        if (is_dir($path)) {
            return $path;
        }

        if (false === $separatorRPos = strrpos($prefix, DIRECTORY_SEPARATOR)) {
            return $scopeInfo->getAbsPathByForTarget('');
        }

        return $scopeInfo->getAbsPathByForTarget(substr($prefix, 0, $separatorRPos));
    }

    private function getDirectoryIteratorForPrefix(ScopeInfoInterface $scopeInfo, $prefix)
    {
        return new \DirectoryIterator($this->getNearestPathForPrefix($scopeInfo, $prefix));
    }

    public function clean(ScopeConfigInterface $scopeConfig)
    {
        $prefix = 'release';
        $scopeInfo = $this->scopeInfoFactory->makeForConfig($scopeConfig);

        $collection = $this->commandFactory->createCollection();
        $directoryIterator = $this->getDirectoryIteratorForPrefix($scopeInfo, $prefix);
        $mentionedTargets = $this->buildScopePaths($scopeInfo, $scopeConfig->getScopeRules());

        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            foreach ($mentionedTargets as $mentionedTarget) {
                if (strpos($mentionedTarget, $fileInfo->getPathname()) === 0) {
                    continue 2;
                }
            }

            $collection->add($this->fsCommandFactory->createRmCommand($fileInfo->getPathname()));
        }

        echo $collection;
    }
}

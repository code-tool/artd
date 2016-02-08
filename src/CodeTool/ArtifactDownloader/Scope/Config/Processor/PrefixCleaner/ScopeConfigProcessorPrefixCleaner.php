<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\PrefixCleaner;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;

class ScopeConfigProcessorPrefixCleaner
{
    /**
     * @var FsCommandFactoryInterface
     */
    private $fsCommandFactory;

    /**
     * @param FsCommandFactoryInterface $fsCommandFactory
     */
    public function __construct(FsCommandFactoryInterface $fsCommandFactory)
    {
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

    /**
     * @param ScopeInfoInterface $scopeInfo
     * @param string             $prefix
     *
     * @return string|false
     */
    private function getNearestPathForPrefix(ScopeInfoInterface $scopeInfo, $prefix)
    {
        $path = $scopeInfo->getAbsPathByForTarget($prefix);

        if (is_dir($path)) {
            return $path;
        }

        if (false === $separatorRPos = strrpos($prefix, DIRECTORY_SEPARATOR)) {
            return $scopeInfo->getAbsPathByForTarget('');
        }

        $result = $scopeInfo->getAbsPathByForTarget(substr($prefix, 0, $separatorRPos));

        if (false === is_dir($result)) {
            return false;
        }

        return $result;
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigInterface       $scopeConfig
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigInterface $scopeConfig
    ) {
        if (null === $scopeConfig->getCleanupPrefix()) {
            return;
        }

        if (false === $nearestPath = $this->getNearestPathForPrefix($scopeInfo, $scopeConfig->getCleanupPrefix())) {
            return;
        }

        $absPathWithPrefix = $scopeInfo->getAbsPathByForTarget($scopeConfig->getCleanupPrefix());
        $mentionedTargets = $this->buildScopePaths($scopeInfo, $scopeConfig->getRules());

        foreach (new \DirectoryIterator($nearestPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $filePathname = $fileInfo->getPathname();

            if (0 !== strpos($filePathname, $absPathWithPrefix)) {
                // Skip path that dose not match to prefix
                continue;
            }

            foreach ($mentionedTargets as $mentionedTarget) {
                if (strpos($mentionedTarget, $filePathname) === 0) {
                    // Skip path that mentioned in config
                    continue 2;
                }
            }

            $collection->add($this->fsCommandFactory->createRmCommand($filePathname));
        }
    }
}

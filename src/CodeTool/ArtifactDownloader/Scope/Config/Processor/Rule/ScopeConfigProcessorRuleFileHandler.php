<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeConfigProcessorRuleFileHandler extends AbstractScopeConfigProcessorRuleHandler implements
    ScopeConfigProcessorRuleHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @param BasicUtil                 $basicUtil
     * @param ResultFactoryInterface    $resultFactory
     * @param CommandFactoryInterface   $commandFactory
     * @param FsCommandFactoryInterface $fsCommandFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        ResultFactoryInterface $resultFactory,
        CommandFactoryInterface $commandFactory,
        FsCommandFactoryInterface $fsCommandFactory
    ) {
        $this->resultFactory = $resultFactory;

        parent::__construct($basicUtil, $commandFactory, $fsCommandFactory);
    }

    /**
     * @return string[]
     */
    public function getSupportedTypes()
    {
        return ['file'];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $realTargetPath
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return string
     */
    private function addForRemoteSource(
        CommandCollectionInterface $collection,
        $source,
        $realTargetPath,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $basicUtil = $this->getBasicUtil();
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();

        $downloadPath = $basicUtil->getTmpPath();
        $collection->add($commandFactory->createDownloadFileCommand($source, $downloadPath));
        $this->addHashCheck($collection, $downloadPath, $scopeConfigRule);
        $unarchivePath = $basicUtil->getRelativeTmpPath($realTargetPath);

        $collection
            ->add($fsCommandFactory->createMkDirCommand($unarchivePath, 0755, true))
            ->add($commandFactory->createUnarchiveCommand(
                $downloadPath,
                $unarchivePath,
                $scopeConfigRule->get(self::CONFIG_RULE_ARCHIVE_FORMAT)
            ))
            ->add($fsCommandFactory->createAssertIsFile($unarchivePath))
            ->add($fsCommandFactory->createRmCommand($downloadPath));

        return $unarchivePath;
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     * @param ScopeInfoInterface         $scopeInfo
     * @param string                     $source
     * @param string                     $realTargetPath
     * @param bool                       $isSourceLocal
     *
     * @return string
     */
    private function getSourceDir(
        CommandCollectionInterface $collection,
        ScopeConfigRuleInterface $scopeConfigRule,
        ScopeInfoInterface $scopeInfo,
        $source,
        $realTargetPath,
        $isSourceLocal = false
    ) {
        if (false === $isSourceLocal) {
            return $this->addForRemoteSource($collection, $source, $realTargetPath, $scopeConfigRule);
        }

        return $this->getPathForTarget($scopeInfo, $source);
    }

    /**
     * @param string $sourceDir
     * @param string $realTargetName
     *
     * @return string
     */
    private function getSourceFilePathFromSourceDir($sourceDir, $realTargetName)
    {
        return sprintf('%s/%s', $sourceDir, $realTargetName);
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $realTarget = $scopeConfigRule->get(self::CONFIG_RULE_TARGET);
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($realTarget);
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        if (false === $targetExists && false === $scopeConfigRule->has(self::CONFIG_RULE_SOURCE)) {
            $collection->add(
                $this->getFsCommandFactory()
                    ->createWriteFileCommand($realTargetPath, $scopeConfigRule->getOrDefault('content', ''))
            );

            return $this->resultFactory->createSuccessful();
        }

        $source = $scopeConfigRule->get(self::CONFIG_RULE_SOURCE);
        $isSourceLocal = $this->getBasicUtil()->isSourceLocal($source);

        $sourceDir = $this->getSourceDir(
            $collection,
            $scopeConfigRule,
            $scopeInfo,
            $source,
            $realTargetPath,
            $isSourceLocal
        );

        $fileSource = $this->getSourceFilePathFromSourceDir($sourceDir, $realTarget);

        if (false === $targetExists) {
            $collection
                ->add($this->getFsCommandFactory()->createMvCommand($fileSource, $realTargetPath))
                ->add($this->getFsCommandFactory()->createRmCommand($sourceDir));

            return $this->resultFactory->createSuccessful();
        }

        $collection->add(
            $this->getCommandFactory()->createCompareFilesCommand(
                $fileSource,
                $realTargetPath,
                $this->getCompareCommand($sourceDir, $isSourceLocal),
                $this->buildSwapOperation($fileSource, $realTargetPath)
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

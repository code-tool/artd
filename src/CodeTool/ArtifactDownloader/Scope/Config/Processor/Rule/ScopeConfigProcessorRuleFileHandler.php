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
     * @param ScopeInfoInterface       $scopeInfo
     * @param ScopeConfigRuleInterface $scopeConfigRule
     *
     * @return bool
     */
    private function isSourceLocal(ScopeInfoInterface $scopeInfo, ScopeConfigRuleInterface $scopeConfigRule)
    {
        if (false === $scopeConfigRule->has(self::CONFIG_RULE_SOURCE)) {
            return false;
        }

        return $this->getBasicUtil()->isSourceLocal($scopeConfigRule->get(self::CONFIG_RULE_SOURCE));
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $realTargetPath
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return string
     */
    private function addCommandsForRemoteSource(
        CommandCollectionInterface $collection,
        $source,
        $realTargetPath,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $downloadPath = $this->getBasicUtil()->getTmpPath();

        $collection->add($this->getCommandFactory()->createDownloadFileCommand($source, $downloadPath));
        $this->addHashCheck($collection, $downloadPath, $scopeConfigRule);

        $unarchivePath = $this->getBasicUtil()->getRelativeTmpPath($realTargetPath);

        $collection
            ->add($this->getFsCommandFactory()->createMkDirCommand($unarchivePath, 0755, true))
            ->add($this->getCommandFactory()->createUnarchiveCommand(
                $downloadPath,
                $unarchivePath,
                $scopeConfigRule->get(self::CONFIG_RULE_ARCHIVE_FORMAT)
            ))
            ->add($this->getFsCommandFactory()->createAssertIsFile($unarchivePath))
            ->add($this->getFsCommandFactory()->createRmCommand($downloadPath));

        return $unarchivePath;
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return string
     */
    private function getSourceFilePathForSource(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $source = $scopeConfigRule->get(self::CONFIG_RULE_SOURCE);

        if ($this->isSourceLocal($scopeInfo, $scopeConfigRule)) {
            $collection->add($this->getFsCommandFactory()->createAssertIsFile($source));

            return $source;
        }

        $realTargetPath = $scopeInfo->getAbsPathByForTarget($scopeConfigRule->get(self::CONFIG_RULE_TARGET));

        return $this->addCommandsForRemoteSource($collection, $source, $realTargetPath, $scopeConfigRule);
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return string
     */
    private function getSourceFilePathForContent(
        CommandCollectionInterface $collection,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $result = $this->getBasicUtil()->getTmpPath();
        $collection->add(
            $this->getFsCommandFactory()
                ->createWriteFileCommand($result, $scopeConfigRule->getOrDefault('content', ''))
        );

        return $result;
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return string
     */
    private function getSourceFilePath(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        if ($scopeConfigRule->has(self::CONFIG_RULE_SOURCE)) {
            $this->getSourceFilePathForSource($collection, $scopeInfo, $scopeConfigRule);
        }

        return $this->getSourceFilePathForContent($collection, $scopeConfigRule);
    }

    /**
     * @param bool   $isSourceLocal
     * @param string $sourceFile
     * @param string $realTargetPath
     *
     * @return \CodeTool\ArtifactDownloader\Command\CommandInterface
     */
    private function buildSwapCommand($isSourceLocal, $sourceFile, $realTargetPath)
    {
        if ($isSourceLocal) {
            return $this->getFsCommandFactory()->createCpCommand($sourceFile, $realTargetPath);
        }

        return $this->getFsCommandFactory()->createMvCommand($sourceFile, $realTargetPath);
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
        $target = $scopeConfigRule->get(self::CONFIG_RULE_TARGET);
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($target);

        $isSourceLocal = $this->isSourceLocal($scopeInfo, $scopeConfigRule);
        $sourceFilePath = $this->getSourceFilePath($collection, $scopeInfo, $scopeConfigRule);
        $swapCommand = $this->buildSwapCommand($isSourceLocal, $sourceFilePath, $realTargetPath);

        if (false === $scopeInfo->isTargetExists($target)) {
            $collection->add($swapCommand);

            return $this->resultFactory->createSuccessful();
        }

        $collection->add(
            $this->getCommandFactory()->createCompareFilesCommand(
                $sourceFilePath,
                $realTargetPath,
                $this->getCommandOnEqual($sourceFilePath, $isSourceLocal),
                $swapCommand
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

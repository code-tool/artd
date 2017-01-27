<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
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
        return [self::CONFIG_RULE_FILE];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $realTargetPath
     * @param DomainObjectInterface      $do
     *
     * @return string
     */
    private function addForRemoteSource(
        CommandCollectionInterface $collection,
        $source,
        $realTargetPath,
        DomainObjectInterface $do
    ) {
        $basicUtil = $this->getBasicUtil();
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();

        $downloadPath = $basicUtil->getTmpPath();
        $collection->add($commandFactory->createDownloadFileCommand($source, $downloadPath));

        $this->addHashCheck($collection, $downloadPath, $do);
        $unarchivePath = $basicUtil->getRelativeTmpPath($realTargetPath);

        $collection
            ->add($fsCommandFactory->createMkDirCommand($unarchivePath, 0755, true))
            ->add($commandFactory->createUnarchiveCommand(
                $downloadPath,
                $unarchivePath,
                $do->get(self::CONFIG_RULE_ARCHIVE_FORMAT)
            ))
            ->add($fsCommandFactory->createAssertIsFile($unarchivePath))
            ->add($fsCommandFactory->createRmCommand($downloadPath));

        return $unarchivePath;
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
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();
        $realTarget = $scopeConfigRule->get(self::CONFIG_RULE_TARGET);
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($realTarget);
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        if (false === $scopeConfigRule->has(self::CONFIG_RULE_SOURCE) && false === $targetExists) {
            $collection->add($fsCommandFactory->createTouchCommand($realTargetPath));

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
        $sourceToRm = $this->getSourceToRm($sourceDir, $fileSource, $isSourceLocal);

        if (false === $targetExists) {
            $collection
                ->add($fsCommandFactory->createCpCommand($fileSource, $realTargetPath))
                ->add($fsCommandFactory->createRmCommand($sourceToRm));

            return $this->resultFactory->createSuccessful();
        }

        $collection->add(
            $commandFactory->createCompareFilesCommand(
                $fileSource,
                $realTargetPath,
                $this->getCompareCommand($sourceDir, $isSourceLocal),
                $this->buildSwapOperation($fileSource, $realTargetPath)
            )
        );

        return $this->resultFactory->createSuccessful();
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
     * @param string $sourceDir
     * @param string $fileSource
     * @param bool   $isLocal
     *
     * @return mixed
     */
    private function getSourceToRm($sourceDir, $fileSource, $isLocal = false)
    {
        if (false === $isLocal) {
            return $sourceDir;
        }

        return $fileSource;
    }
}

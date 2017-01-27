<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeConfigProcessorRuleDirHandler extends AbstractScopeConfigProcessorRuleHandler implements
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
        return [self::CONFIG_RULE_DIR];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $target
     * @param DomainObjectInterface      $do
     */
    private function addGMOCommands(CommandCollectionInterface $collection, $target, DomainObjectInterface $do)
    {
        $fsCommand = $this->getFsCommandFactory();

        if ($do->has(self::CONFIG_RULE_GROUP)) {
            $collection->add($fsCommand->createChgrpCommand($target, $do->get(self::CONFIG_RULE_GROUP)));
        }

        if ($do->has(self::CONFIG_RULE_OWNER)) {
            $collection->add($fsCommand->createChownCommand($target, $do->get(self::CONFIG_RULE_OWNER)));
        }

        if ($do->has(self::CONFIG_RULE_MODE)) {
            $collection->add($fsCommand->createChmodCommand($target, $do->get(self::CONFIG_RULE_MODE)));
        }
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $target
     * @param DomainObjectInterface      $do
     */
    private function addPermissionsCommand(CommandCollectionInterface $collection, $target, DomainObjectInterface $do)
    {
        if (false === $do->has(self::CONFIG_RULE_PERMISSIONS)) {
            return;
        }

        $collection->add($this->getFsCommandFactory()
            ->createPermissionsCommandFromStr(
                $target,
                $do->get(self::CONFIG_RULE_PERMISSIONS)
            )
        );
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $target
     * @param DomainObjectInterface      $do
     *
     * @return string
     */
    private function addForRemoteSource(
        CommandCollectionInterface $collection,
        $source,
        $target,
        DomainObjectInterface $do
    ) {
        $basicUtil = $this->getBasicUtil();
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();

        // download file
        $downloadPath = $basicUtil->getTmpPath();
        $collection->add($commandFactory->createDownloadFileCommand($source, $downloadPath));

        // check hash if needed
        $this->addHashCheck($collection, $downloadPath, $do);

        $unarchivePath = $basicUtil->getRelativeTmpPath($target);

        // unarchive
        $collection
            ->add($fsCommandFactory->createMkDirCommand($unarchivePath, 0755, true))
            ->add($commandFactory->createUnarchiveCommand(
                $downloadPath,
                $unarchivePath,
                $do->get(self::CONFIG_RULE_ARCHIVE_FORMAT)
            ))
            ->add($fsCommandFactory->createAssertIsDir($unarchivePath))
            ->add($fsCommandFactory->createRmCommand($downloadPath));

        return $unarchivePath;
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $target
     * @param DomainObjectInterface      $do
     */
    private function addForLocalSource(
        CommandCollectionInterface $collection,
        $source,
        $target,
        DomainObjectInterface $do
    ) {
        $this->addHashCheck($collection, $source, $do);

        if (true === $do->has(self::CONFIG_RULE_ARCHIVE_FORMAT)) {
            $collection->add(
                $this->getCommandFactory()->createUnarchiveCommand($source, $target, $do->get(self::CONFIG_RULE_ARCHIVE_FORMAT))
            );
        } else {
            $collection->add($this->getFsCommandFactory()->createCpCommand($source, $target));
        }
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
        $basicUtil = $this->getBasicUtil();
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();
        $realTarget = $scopeConfigRule->get(self::CONFIG_RULE_TARGET);
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($realTarget);
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        // If no source defined
        if (false === $scopeConfigRule->has(self::CONFIG_RULE_SOURCE)) {
            if (false === $targetExists) {
                // And directory dose not exists. Just create new
                $collection->add($fsCommandFactory->createMkDirCommand($realTargetPath, 0755, true));
            }

            // Fix permissions, if need
            $this->addGMOCommands($collection, $realTargetPath, $scopeConfigRule);
            $this->addPermissionsCommand($collection, $realTargetPath, $scopeConfigRule);

            return $this->resultFactory->createSuccessful();
        }

        // Source defined
        $source = $scopeConfigRule->get(self::CONFIG_RULE_SOURCE);
        $isSourceLocal = $basicUtil->isSourceLocal($source);

        if (false === $isSourceLocal) {
            // If source remote, download it and get new source path
            $source = $this->addForRemoteSource($collection, $source, $realTargetPath, $scopeConfigRule);
            // Fix permissions
            $this->addGMOCommands($collection, $source, $scopeConfigRule);
            $this->addPermissionsCommand($collection, $source, $scopeConfigRule);
        } else {
            // todo: Fix case when source is local directory. Is source absolute path?
            $source = $scopeInfo->getAbsPathByForTarget($source);
        }

        if (false === $targetExists) {
            // Now, if target dose not exists, just move
            $collection->add($fsCommandFactory->createMvCommand($source, $realTargetPath));

            return $this->resultFactory->createSuccessful();
        }

        // So, if target exists, we should compare directories
        $collection->add(
            $commandFactory->createCompareDirsCommand(
                $source,
                $realTargetPath,
                // if its equals, remove source (only for remote)
                $this->getCompareCommand($source, $isSourceLocal),
                // else, swap
                $this->buildSwapOperation($source, $realTargetPath)
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

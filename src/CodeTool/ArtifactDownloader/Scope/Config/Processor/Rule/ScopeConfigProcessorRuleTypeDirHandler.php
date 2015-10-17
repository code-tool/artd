<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeConfigProcessorRuleTypeDirHandler implements ScopeConfigProcessorRuleTypeHandlerInterface
{
    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @param BasicUtil               $basicUtil
     * @param ResultFactoryInterface  $resultFactory
     * @param CommandFactoryInterface $commandFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        ResultFactoryInterface $resultFactory,
        CommandFactoryInterface $commandFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->resultFactory = $resultFactory;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @return string[]
     */
    public function getSupportedTypes()
    {
        return ['dir'];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $target
     * @param DomainObjectInterface      $do
     */
    private function addGMOCommands(CommandCollectionInterface $collection, $target, DomainObjectInterface $do)
    {
        if ($do->has('group')) {
            $collection->add($this->commandFactory->createChgrpCommand($target, $do->get('group')));
        }

        if ($do->has('owner')) {
            $collection->add($this->commandFactory->createChownCommand($target, $do->get('owner')));
        }

        if ($do->has('mode')) {
            $collection->add($this->commandFactory->createChmodCommand($target, $do->get('mode')));
        }
    }

    /**
     * Move to separate class ?
     *
     * @param CommandCollectionInterface $collection
     * @param string                     $target
     * @param DomainObjectInterface      $do
     *
     * @return bool
     */
    private function addHashCheck(CommandCollectionInterface $collection, $target, DomainObjectInterface $do)
    {
        if (null === ($hash = $do->getOrDefault('hash', null))) {
            return false;
        }

        $algorithm = CommandCheckFileSignature::DEFAULT_ALGORITHM;
        if (false !== ($delimiterPos = strpos($hash, ':'))) {
            $algorithm = substr($hash, 0, $delimiterPos);
            $hash = substr($hash, $delimiterPos + 1);
        }

        $collection->add($this->commandFactory->createCheckFileSignatureCommand($target, $hash, $algorithm));

        return true;
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
        // download file
        $downloadPath = $this->basicUtil->getTmpPath();
        $collection->add($this->commandFactory->createDownloadFileCommand($source, $downloadPath));

        // check hash if needed
        $this->addHashCheck($collection, $downloadPath, $do);

        $unarchivePath = $this->basicUtil->getRelativeTmpPath($target);

        // unarchive
        $collection
            ->add($this->commandFactory->createMkDirCommand($unarchivePath, 0755, true))
            ->add($this->commandFactory->createUnarchiveCommand(
                $downloadPath,
                $unarchivePath,
                $do->get('archive_format')
            ))
            ->add($this->commandFactory->createRmCommand($downloadPath));

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

        if (true === $do->has('archive_format')) {
            $collection->add(
                $this->commandFactory->createUnarchiveCommand($source, $target, $do->get('archive_format'))
            );
        } else {
            $collection->add($this->commandFactory->createCopyFileCommand($source, $target));
        }
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandCollectionInterface
     */
    private function buildSwapOperation($sourcePath, $targetPath)
    {
        // todo If source local, do not move, just copy
        $newTargetPath = $this->basicUtil->getRelativeTmpPath($targetPath);
        // double move. target to tmp path, after source to target and remove tmp
        $result = $this->commandFactory->createCollection()
            ->add($this->commandFactory->createMoveFileCommand($targetPath, $newTargetPath))
            ->add($this->commandFactory->createMoveFileCommand($sourcePath, $targetPath))
            ->add($this->commandFactory->createRmCommand($newTargetPath));

        return $result;
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
        $realTarget = $scopeConfigRule->get('target');
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($realTarget);
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        // If no source defined
        if (false === $scopeConfigRule->has('source')) {
            if (false === $targetExists) {
                // And directory dose not exists. Just create new
                $collection->add($this->commandFactory->createMkDirCommand($realTargetPath, 0755, true));
            }

            // Fix permissions, if need
            $this->addGMOCommands($collection, $realTargetPath, $scopeConfigRule);

            return $this->resultFactory->createSuccessful();
        }

        // Source defined
        $source = $scopeConfigRule->get('source');
        $isSourceLocal = $this->basicUtil->isSourceLocal($source);

        if (false === $isSourceLocal) {
            // If source remote, download it and get new source path
            $source = $this->addForRemoteSource($collection, $source, $realTargetPath, $scopeConfigRule);
        } else {
            // todo Is source absolute path?
            $source = $scopeInfo->getAbsPathByForTarget($source);
        }

        if (false === $targetExists) {
            // Now, if target dose not exists, just move
            $collection->add($this->commandFactory->createMoveFileCommand($source, $realTargetPath));
            // Fix permissions, if need
            $this->addGMOCommands($collection, $realTargetPath, $scopeConfigRule);

            return $this->resultFactory->createSuccessful();
        }

        $successCompareCommand = $isSourceLocal
            ? $this->commandFactory->createNopCommand()
            : $this->commandFactory->createRmCommand($source);

        // So, if target exists, we should compare directories
        $collection->add(
            $this->commandFactory->createCompareDirsCommand(
                $source,
                $realTargetPath,
                // if its equals, remove source (only for remote)
                $successCompareCommand,
                // else, swap
                $this->buildSwapOperation($source, $realTargetPath)
            )
        );

        return $this->resultFactory->createSuccessful();
    }
}

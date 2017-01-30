<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

abstract class AbstractScopeConfigProcessorRuleHandler
{
    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @var FsCommandFactoryInterface
     */
    private $fsCommandFactory;

    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @param BasicUtil                 $basicUtil
     * @param CommandFactoryInterface   $commandFactory
     * @param FsCommandFactoryInterface $fsCommandFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        CommandFactoryInterface $commandFactory,
        FsCommandFactoryInterface $fsCommandFactory
    ) {
        $this->commandFactory = $commandFactory;
        $this->fsCommandFactory = $fsCommandFactory;
        $this->basicUtil = $basicUtil;
    }

    /**
     * @return CommandFactoryInterface
     */
    protected function getCommandFactory()
    {
        return $this->commandFactory;
    }

    /**
     * @return FsCommandFactoryInterface
     */
    protected function getFsCommandFactory()
    {
        return $this->fsCommandFactory;
    }

    /**
     * @return BasicUtil
     */
    protected function getBasicUtil()
    {
        return $this->basicUtil;
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
    protected function addHashCheck(CommandCollectionInterface $collection, $target, DomainObjectInterface $do)
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
     * @param bool   $isSourceLocal
     * @param string $source
     *
     * @return CommandInterface
     */
    protected function getCompareCommand($source, $isSourceLocal = false)
    {
        if (true === $isSourceLocal) {
            return $this->commandFactory->createNopCommand();
        }

        return $this->fsCommandFactory->createRmCommand($source);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandCollectionInterface
     */
    protected function buildSwapOperation($sourcePath, $targetPath)
    {
        $commandFactory = $this->getCommandFactory();
        $fsCommandFactory = $this->getFsCommandFactory();

        // todo If source local, do not move, just copy
        $newTargetPath = $this->basicUtil->getRelativeTmpPath($targetPath);

        // double move. target to tmp path, after source to target and remove tmp
        $result = $commandFactory->createCollection()
            ->add($fsCommandFactory->createMvCommand($targetPath, $newTargetPath))
            ->add($fsCommandFactory->createMvCommand($sourcePath, $targetPath))
            ->add($fsCommandFactory->createRmCommand($newTargetPath));

        return $result;
    }

    /**
     * @param ScopeInfoInterface $scopeInfo
     * @param                    $sourceTarget
     *
     * @return string
     */
    protected function getPathForTarget(ScopeInfoInterface $scopeInfo, $sourceTarget)
    {
        if (true === $scopeInfo->isPathForTargetIsAbs($sourceTarget)) {
            return $sourceTarget;
        }

        return $scopeInfo->getAbsPathByForTarget($sourceTarget);
    }
}

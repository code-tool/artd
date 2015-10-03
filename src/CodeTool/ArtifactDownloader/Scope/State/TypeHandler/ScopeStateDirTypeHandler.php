<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeStateDirTypeHandler implements ScopeStateTypeHandlerInterface
{
    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    public function __construct(BasicUtil $basicUtil, CommandFactoryInterface $commandFactory)
    {
        $this->basicUtil = $basicUtil;
        $this->commandFactory = $commandFactory;
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
        if (false === $do->has('hash')) {
            return false;
        }

        $collection->add($this->commandFactory->createCheckFileSignatureCommand($target, $do->get('hash')));

        return true;
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    private function isSourceLocal($source)
    {
        return false === strpos($source, '://');
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
            ->add($this->commandFactory->createMkDirCommand($unarchivePath))
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

    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    ) {
        if ('dir' !== $scopeConfigChildNode->getType()) {
            return false;
        }

        $realTarget = $scopeConfigChildNode->get('target');
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($realTarget);
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        // If no source defined
        if (false === $scopeConfigChildNode->has('source')) {
            if (false === $targetExists) {
                // And directory dose not exists. Just create new
                $collection->add($this->commandFactory->createMkDirCommand($realTargetPath, 0777, true));
            }

            // Fix permissions, if need
            $this->addGMOCommands($collection, $realTargetPath, $scopeConfigChildNode);

            return true;
        }

        // Source defined
        $source = $scopeConfigChildNode->get('source');
        $isSourceLocal = $this->basicUtil->isSourceLocal($source);

        if (false === $isSourceLocal) {
            // If source remote, download it and get new source path
            $source = $this->addForRemoteSource($collection, $source, $realTargetPath, $scopeConfigChildNode);
        } else {
            // todo Is source absolute path?
            $source = $scopeInfo->getAbsPathByForTarget($source);
        }

        if (false === $targetExists) {
            // Now, if target dose not exists, just move
            $collection->add($this->commandFactory->createMoveFileCommand($source, $realTargetPath));
            // Fix permissions, if need
            $this->addGMOCommands($collection, $realTargetPath, $scopeConfigChildNode);

            return true;
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

        return true;
    }
}

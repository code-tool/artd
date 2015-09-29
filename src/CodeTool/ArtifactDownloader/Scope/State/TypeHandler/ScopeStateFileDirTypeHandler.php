<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\CommandCollectionBuilder\Section\SourceSectionCollectionBuilder;
use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Scope\State\SectionHandler\ScopeStateGMOSectionHandler;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeStateFileDirTypeHandler implements ScopeStateTypeHandlerInterface
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
        return false === strpos('://', $source);
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param string                     $source
     * @param string                     $target
     * @param DomainObjectInterface      $do
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

        $moveSourcePath = $downloadPath;
        if (true === $do->has('archive_format')) {
            $moveSourcePath = $this->basicUtil->getTmpPath();

            // unarchive
            $collection
                ->add($this->commandFactory->createUnarchiveCommand(
                    $downloadPath,
                    $moveSourcePath,
                    $do->get('archive_format')
                ))
                ->add($this->commandFactory->createRmCommand($downloadPath));
        }

        $collection->add($this->commandFactory->createMoveFileCommand($moveSourcePath, $target));
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

    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    ) {
        $type = $scopeConfigChildNode->getType();
        if ('file' !== $type && 'dir' !== $type) {
            return false;
        }

        $realTarget = $scopeConfigChildNode->get('target');
        $targetExists = $scopeInfo->isTargetExists($realTarget);

        $target = $scopeInfo->getAbsPathByForTarget($realTarget);

        if ($targetExists) {
            $target = $this->basicUtil->getTmpPath();
        }

        // $content => setContent
        if ($scopeConfigChildNode->has('source')) {
            $source = $scopeConfigChildNode->get('source');

            if (false === $this->isSourceLocal($source)) {
                $this->addForRemoteSource($collection, $source, $target, $scopeConfigChildNode);
            } else {
                $this->addForLocalSource(
                    $collection,
                    $scopeInfo->getAbsPathByForTarget($source),
                    $target,
                    $scopeConfigChildNode
                );
            }
        } // Todo Handle other cases

        // todo Add file/dir check

        $this->addGMOCommands($collection, $target, $scopeConfigChildNode);

        if ($targetExists) {
            $collection->add(
                $this->commandFactory->createMoveFileCommand($target, $scopeInfo->getAbsPathByForTarget($realTarget))
            );
        }

        return true;
    }
}

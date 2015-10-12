<?php

namespace CodeTool\ArtifactDownloader\Scope\State\TypeHandler;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeStateSymlinkTypeHandler implements ScopeStateTypeHandlerInterface
{
    public function __construct(
        BasicUtil $basicUtil,
        CommandFactoryInterface $commandFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->commandFactory = $commandFactory;
    }

    public function handle(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigChildNodeInterface $scopeConfigChildNode
    ) {
        if ('symlink' !== $scopeConfigChildNode->getType()) {
            return false;
        }

        // Symlink name
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($scopeConfigChildNode->get('target'));
        $realSourcePath = $scopeInfo->getAbsPathByForTarget($scopeConfigChildNode->get('source'));

        if (false === file_exists($realTargetPath) && false === is_link($realTargetPath)) {
            // Target dose not exists, just create symlink
            $collection->add($this->commandFactory->createSymlinkCommand($realTargetPath, $realSourcePath));

            return true;
        }

        $relativeTmpPath = $this->basicUtil->getRelativeTmpPath($realTargetPath);

        if (false === is_link($realTargetPath)) {
            // Target is not symlink
            $collection
                ->add($this->commandFactory->createRenameCommand($realTargetPath, $relativeTmpPath))
                ->add($this->commandFactory->createSymlinkCommand($realTargetPath, $realSourcePath))
                ->add($this->commandFactory->createRmCommand($relativeTmpPath));

            return true;
        }

        if ($realSourcePath !== readlink($realTargetPath)) {
            // Link with same name already exists, but reference to other path
            $collection
                ->add($this->commandFactory->createRmCommand($realTargetPath))
                ->add($this->commandFactory->createSymlinkCommand($relativeTmpPath, $realSourcePath));

            return true;
        }

        return true;
    }
}

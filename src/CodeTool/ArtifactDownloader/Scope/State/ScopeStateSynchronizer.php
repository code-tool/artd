<?php

namespace CodeTool\ArtifactDownloader\Scope\State;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNodeInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class ScopeStateSynchronizer
{
    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        CommandFactoryInterface $commandFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->commandFactory = $commandFactory;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPathState($path)
    {
        if (true === is_dir($path)) {
            return 'directory';
        }

        if (true === is_link($path)) {
            return 'symlink';
        }

        return 'file';
    }

    private function getScopeTopObjects()
    {
        $result = [];

        foreach (scandir($this->scopeConfig->getScopePath()) as $fileOrDir) {
            if ('.' === $fileOrDir || '..' === $fileOrDir) {
                continue;
            }

            $result[$fileOrDir] = $this->getPathState(
                $this->scopeConfig->getScopePath() . DIRECTORY_SEPARATOR . $fileOrDir
            );
        }

        return $result;
    }

    private function getTmpPath()
    {
        return tempnam(sys_get_temp_dir(), 'arifact-downloader-' . posix_getpid());
    }

    private function getAbsScopePath($internalPath)
    {
        return $this->scopeConfig->getScopePath() . DIRECTORY_SEPARATOR . $internalPath;
    }

    /**
     * @param string $sourcePath
     *
     * @return string
     */
    private function getExtension($sourcePath)
    {
        $lastDotPosition = null;

        for ($pos = strlen($sourcePath) - 1; $pos > 0; $pos--) {
            if ('.' === $sourcePath[$pos]) {
                $lastDotPosition = $pos;
            }

            if ('/' === $sourcePath[$pos]) {
                break;
            }
        }

        if (null === $lastDotPosition) {
            return '';
        }

        return substr($sourcePath, $lastDotPosition);
    }

    private function addCreateDirectoryFromSourceOps(
        CommandCollectionInterface $commandCollection,
        $targetPath,
        $sourcePath,
        $sourceHash
    ) {
        $downloadPath = $this->getTmpPath() . $this->getExtension($sourcePath);
        $commandCollection->add($this->commandFactory->createDownloadFileCommand($sourcePath, $downloadPath));

        if (null !== $sourceHash) {
            $commandCollection->add($this->commandFactory->createCheckFileSignatureCommand($downloadPath, $sourceHash));
        }

        $commandCollection
            ->add($this->commandFactory->createUnpackArchiveCommand($downloadPath, $targetPath))
            ->add($this->commandFactory->createRmCommand($downloadPath));
    }

    private function addCreateDirectoryOps(
        CommandCollectionInterface $commandCollection,
        ScopeConfigChildNodeInterface $childNode,
        $overwrite
    ) {
        $realTargetPath = $this->getAbsScopePath($childNode->getRelativePath());
        if (true === $overwrite) {
            $targetPath = $this->getTmpPath();
        } else {
            $targetPath = $realTargetPath;
        }

        if (null !== $childNode->getSourcePath()) {
            $this->addCreateDirectoryFromSourceOps($commandCollection, $targetPath, $childNode->getSourcePath(), $childNode->getSourceHash());
        } else {
            $commandCollection->add($this->commandFactory->createMkDirCommand($targetPath, 0775));
        }

        $commandCollection->add($this->commandFactory->createMoveFileCommand($targetPath, $realTargetPath));
    }

    public function sync()
    {
        $scopeTopObjects = [];
        $commandCollection = $this->commandFactory->createCollection();

        if (false === is_dir($this->scopeConfig->getScopePath())) {
            $commandCollection->add($this->commandFactory->createMkDirCommand(
                $this->scopeConfig->getScopePath(), 0775, true
            ));
        } else {
            $scopeTopObjects = $this->getScopeTopObjects();
        }

        foreach ($this->scopeConfig->getChildNodes() as $childNode) {
            if (true === ($needOverwrite = array_key_exists($childNode->getRelativePath(), $scopeTopObjects))) {
                // todo -> fix me
                $sameState = $scopeTopObjects[$childNode->getRelativePath()] === $childNode->getType();

                unset($scopeTopObjects[$childNode->getRelativePath()]);

                if ($sameState) {
                    // Scope object in the same state
                    continue;
                }
            }

            switch ($childNode->getType()) {
                case 'directory':
                    $this->addCreateDirectoryOps($commandCollection, $childNode, $needOverwrite);
                    break;
                case 'symlink':
                    break;
            }
        }

        // Remove remain scope objects
        foreach ($scopeTopObjects as $scopeTopObjectName => $scopeTopObjectType) {
            $commandCollection->add(
                $this->commandFactory->createRmCommand($this->getAbsScopePath($scopeTopObjectName))
            );
        }

        return $commandCollection->execute();
    }
}

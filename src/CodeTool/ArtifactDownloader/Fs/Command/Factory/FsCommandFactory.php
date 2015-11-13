<?php

namespace CodeTool\ArtifactDownloader\Fs\Command\Factory;

use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChgrp;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChmod;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChown;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandCp;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandMkDir;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandMv;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandRename;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandRm;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandSymlink;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;

/**
 * Class FsCommandFactory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @package CodeTool\ArtifactDownloader\Fs\Command\Factory
 */
class FsCommandFactory implements FsCommandFactoryInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @param ResultFactoryInterface $resultFactory
     */
    public function __construct(ResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandMv
     */
    public function createMvCommand($sourcePath, $targetPath)
    {
        return new FsCommandMv($this->resultFactory, $sourcePath, $targetPath);
    }

    /**
     * @param string $filePath
     * @param string $mode
     *
     * @return FsCommandChmod
     */
    public function createChmodCommand($filePath, $mode)
    {
        return new FsCommandChmod($this->resultFactory, $filePath, $mode);
    }

    /**
     * @param string $filePath
     * @param string $user
     *
     * @return FsCommandChown
     */
    public function createChownCommand($filePath, $user)
    {
        return new FsCommandChown($this->resultFactory, $filePath, $user);
    }

    /**
     * @param string $filePath
     * @param string $group
     *
     * @return FsCommandChgrp
     */
    public function createChgrpCommand($filePath, $group)
    {
        return new FsCommandChgrp($this->resultFactory, $filePath, $group);
    }

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return FsCommandMkDir
     */
    public function createMkDirCommand($path, $mode = 0777, $recursive = false)
    {
        return new FsCommandMkDir($this->resultFactory, $path, $mode, $recursive);
    }

    /**
     * @param string $path
     *
     * @return FsCommandRm
     */
    public function createRmCommand($path)
    {
        return new FsCommandRm($this->resultFactory, $path);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandCp
     */
    public function createCpCommand($sourcePath, $targetPath)
    {
        return new FsCommandCp($this->resultFactory, $sourcePath, $targetPath);
    }

    /**
     * @param string $name
     * @param string $targetPath
     *
     * @return FsCommandSymlink
     */
    public function createSymlinkCommand($name, $targetPath)
    {
        return new FsCommandSymlink($this->resultFactory, $name, $targetPath);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandRename
     */
    public function createRenameCommand($sourcePath, $targetPath)
    {
        return new FsCommandRename($this->resultFactory, $sourcePath, $targetPath);
    }
}

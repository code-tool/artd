<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandChgrp;
use CodeTool\ArtifactDownloader\Command\CommandChmod;
use CodeTool\ArtifactDownloader\Command\CommandChown;
use CodeTool\ArtifactDownloader\Command\CommandCompareDirs;
use CodeTool\ArtifactDownloader\Command\CommandCopyFile;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\CommandMkDir;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandNop;
use CodeTool\ArtifactDownloader\Command\CommandRm;
use CodeTool\ArtifactDownloader\Command\CommandSymlink;
use CodeTool\ArtifactDownloader\Command\CommandUnarchive;

interface CommandFactoryInterface
{
    /**
     * @param string $filePath
     * @param string $expectedHash
     * @param string $algorithm
     *
     * @return CommandCheckFileSignature
     */
    public function createCheckFileSignatureCommand(
        $filePath,
        $expectedHash,
        $algorithm = CommandCheckFileSignature::DEFAULT_ALGORITHM
    );

    /**
     * @param string $url
     * @param string $target
     *
     * @return CommandDownloadFile
     */
    public function createDownloadFileCommand($url, $target);

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandMoveFile
     */
    public function createMoveFileCommand($sourcePath, $targetPath);

    /**
     * @param string $filePath
     * @param string $mode
     *
     * @return CommandChmod
     */
    public function createChmodCommand($filePath, $mode);

    /**
     * @param string $filePath
     * @param string $user
     *
     * @return CommandChown
     */
    public function createChownCommand($filePath, $user);

    /**
     * @param string $filePath
     * @param string $group
     *
     * @return CommandChgrp
     */
    public function createChgrpCommand($filePath, $group);

    /**
     * @param string $source
     * @param string $target
     * @param string $archiveFormat
     *
     * @return CommandUnarchive
     */
    public function createUnarchiveCommand($source, $target, $archiveFormat);

    /**
     * @return CommandCollectionInterface
     */
    public function createCollection();

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return CommandMkDir
     */
    public function createMkDirCommand($path, $mode = 0777, $recursive = false);

    /**
     * @param string $path
     *
     * @return CommandRm
     */
    public function createRmCommand($path);

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandCopyFile
     */
    public function createCopyFileCommand($sourcePath, $targetPath);

    /**
     * @param string $name
     * @param string $sourcePath
     *
     * @return CommandSymlink
     */
    public function createSymlinkCommand($name, $sourcePath);

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandSymlink
     */
    public function createRenameCommand($sourcePath, $targetPath);

    /**
     * @return CommandNop
     */
    public function createNopCommand();

    /**
     * @param string           $sourcePath
     * @param string           $targetPath
     * @param CommandInterface $onEqualCommand
     * @param CommandInterface $onNotEqualCommand
     *
     * @return CommandCompareDirs
     */
    public function createCompareDirsCommand(
        $sourcePath,
        $targetPath,
        CommandInterface $onEqualCommand,
        CommandInterface $onNotEqualCommand
    );
}

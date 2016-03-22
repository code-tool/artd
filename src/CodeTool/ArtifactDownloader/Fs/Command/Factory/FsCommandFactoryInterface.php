<?php

namespace CodeTool\ArtifactDownloader\Fs\Command\Factory;

use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChgrp;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChmod;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandChown;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandCp;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandMkDir;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandMv;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandPermissions;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandRename;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandRm;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandSymlink;
use CodeTool\ArtifactDownloader\Fs\Command\FsCommandWriteFile;

interface FsCommandFactoryInterface
{
    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandMv
     */
    public function createMvCommand($sourcePath, $targetPath);

    /**
     * @param string $filePath
     * @param string $mode
     *
     * @return FsCommandChmod
     */
    public function createChmodCommand($filePath, $mode);

    /**
     * @param string $filePath
     * @param string $user
     *
     * @return FsCommandChown
     */
    public function createChownCommand($filePath, $user);

    /**
     * @param string $filePath
     * @param string $group
     *
     * @return FsCommandChgrp
     */
    public function createChgrpCommand($filePath, $group);

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return FsCommandMkDir
     */
    public function createMkDirCommand($path, $mode = 0777, $recursive = false);

    /**
     * @param string $path
     *
     * @return FsCommandRm
     */
    public function createRmCommand($path);

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandCp
     */
    public function createCpCommand($sourcePath, $targetPath);

    /**
     * @param string $name
     * @param string $targetPath
     *
     * @return FsCommandSymlink
     */
    public function createSymlinkCommand($name, $targetPath);

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return FsCommandRename
     */
    public function createRenameCommand($sourcePath, $targetPath);

    /**
     * @param string $path
     * @param string $permissions
     *
     * @return FsCommandPermissions
     */
    public function createPermissionsCommandFromStr($path, $permissions);

    /**
     * @param string $path
     * @param string $content
     *
     * @return FsCommandWriteFile
     */
    public function createWriteFileCommand($path, $content);
}

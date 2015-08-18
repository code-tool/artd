<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandSetFilePermissions;
use CodeTool\ArtifactDownloader\Command\CommandUnpackArchive;

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
     * @return CommandSetFilePermissions
     */
    public function createSetFilePermissionsCommand($filePath, $mode);

    /**
     * @param string $path
     * @param string $target
     *
     * @return CommandUnpackArchive
     */
    public function createUnpackArchiveCommand($path, $target);

    /**
     * @return CommandCollection
     */
    public function createCollection();
}

<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandCompareDirs;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandFcgiRequest;
use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\CommandNop;
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

    /**
     * @param string   $socketPath
     * @param string[] $headers
     * @param string   $stdin
     *
     * @return CommandFcgiRequest
     */
    public function createFcgiRequestCommand($socketPath, array $headers, $stdin);
}

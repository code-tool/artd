<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandSetFilePermissions;
use CodeTool\ArtifactDownloader\Command\CommandUnpackArchive;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory)
    {
        $this->commandResultFactory = $commandResultFactory;
    }

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
    ) {
        return new CommandCheckFileSignature($this->commandResultFactory, $filePath, $expectedHash, $algorithm);
    }

    /**
     * @param string $url
     * @param string $target
     *
     * @return CommandDownloadFile
     */
    public function createDownloadFileCommand($url, $target)
    {
        return new CommandDownloadFile($this->commandResultFactory, $url, $target);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandMoveFile
     */
    public function createMoveFileCommand($sourcePath, $targetPath)
    {
        return new CommandMoveFile($this->commandResultFactory, $sourcePath, $targetPath);
    }

    /**
     * @param string $filePath
     * @param string $mode
     *
     * @return CommandSetFilePermissions
     */
    public function createSetFilePermissionsCommand($filePath, $mode)
    {
        return new CommandSetFilePermissions($this->commandResultFactory, $filePath, $mode);
    }

    /**
     * @param string $path
     * @param string $target
     *
     * @return CommandUnpackArchive
     */
    public function createUnpackArchiveCommand($path, $target)
    {
        return new CommandUnpackArchive($this->commandResultFactory, $path, $target);
    }
}

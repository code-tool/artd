<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandCopyFile;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandMkDir;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandRm;
use CodeTool\ArtifactDownloader\Command\CommandSetFilePermissions;
use CodeTool\ArtifactDownloader\Command\CommandUnpackArchive;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var ResourceCredentialsRepositoryInterface
     */
    private $resourceCredentialsRepository;

    /**
     * @param CommandResultFactoryInterface          $commandResultFactory
     * @param ResourceCredentialsRepositoryInterface $resourceCredentialsRepository
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
        ResourceCredentialsRepositoryInterface $resourceCredentialsRepository
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->resourceCredentialsRepository = $resourceCredentialsRepository;
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
        return new CommandDownloadFile(
            $this->commandResultFactory,
            $this->resourceCredentialsRepository,
            $url,
            $target
        );
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

    /**
     * @return CommandCollection
     */
    public function createCollection()
    {
        return new CommandCollection($this->commandResultFactory);
    }

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return CommandMkDir
     */
    public function createMkDirCommand($path, $mode = 0777, $recursive = false)
    {
        return new CommandMkDir($this->commandResultFactory, $path, $mode, $recursive);
    }

    /**
     * @param string $path
     *
     * @return CommandRm
     */
    public function createRmCommand($path)
    {
        return new CommandRm($this->commandResultFactory, $path);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandCopyFile
     */
    public function createCopyFileCommand($sourcePath, $targetPath)
    {
        return new CommandCopyFile($this->commandResultFactory, $sourcePath, $targetPath);
    }
}

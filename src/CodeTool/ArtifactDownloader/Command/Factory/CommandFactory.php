<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandChgrp;
use CodeTool\ArtifactDownloader\Command\CommandChown;
use CodeTool\ArtifactDownloader\Command\CommandCopyFile;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandMkDir;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandRm;
use CodeTool\ArtifactDownloader\Command\CommandChmod;
use CodeTool\ArtifactDownloader\Command\CommandSymlink;
use CodeTool\ArtifactDownloader\Command\CommandUnpackArchive;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param HttpClientInterface           $httpClient
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
        HttpClientInterface $httpClient
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->httpClient = $httpClient;
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
            $this->httpClient,
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
     * @return CommandChmod
     */
    public function createChmodCommand($filePath, $mode)
    {
        return new CommandChmod($this->commandResultFactory, $filePath, $mode);
    }

    /**
     * @param string $filePath
     * @param string $user
     *
     * @return CommandChown
     */
    public function createChownCommand($filePath, $user)
    {
        return new CommandChown($this->commandResultFactory, $filePath, $user);
    }

    /**
     * @param string $filePath
     * @param string $group
     *
     * @return CommandChgrp
     */
    public function createChgrpCommand($filePath, $group)
    {
        return new CommandChgrp($this->commandResultFactory, $filePath, $group);
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

    /**
     * @param string $targetPath
     * @param string $sourcePath
     *
     * @return CommandSymlink
     */
    public function createSymlinkCommand($targetPath, $sourcePath)
    {
        return new CommandSymlink($this->commandResultFactory, $targetPath, $sourcePath);
    }
}

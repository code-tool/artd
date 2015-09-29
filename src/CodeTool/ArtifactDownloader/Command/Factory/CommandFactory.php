<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Archive\Factory\UnarchiverFactoryInterface;
use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandChgrp;
use CodeTool\ArtifactDownloader\Command\CommandChmod;
use CodeTool\ArtifactDownloader\Command\CommandChown;
use CodeTool\ArtifactDownloader\Command\CommandCopyFile;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandMkDir;
use CodeTool\ArtifactDownloader\Command\CommandMoveFile;
use CodeTool\ArtifactDownloader\Command\CommandRm;
use CodeTool\ArtifactDownloader\Command\CommandSymlink;
use CodeTool\ArtifactDownloader\Command\CommandUnarchive;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;

class CommandFactory implements CommandFactoryInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var UnarchiverFactoryInterface
     */
    private $unarchiverFactory;

    /**
     * @param ResultFactoryInterface     $resultFactory
     * @param HttpClientInterface        $httpClient
     * @param UnarchiverFactoryInterface $unarchiverFactory
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        HttpClientInterface $httpClient,
        UnarchiverFactoryInterface $unarchiverFactory
    ) {
        $this->resultFactory = $resultFactory;
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
        return new CommandCheckFileSignature($this->resultFactory, $filePath, $expectedHash, $algorithm);
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
            $this->resultFactory,
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
        return new CommandMoveFile($this->resultFactory, $sourcePath, $targetPath);
    }

    /**
     * @param string $filePath
     * @param string $mode
     *
     * @return CommandChmod
     */
    public function createChmodCommand($filePath, $mode)
    {
        return new CommandChmod($this->resultFactory, $filePath, $mode);
    }

    /**
     * @param string $filePath
     * @param string $user
     *
     * @return CommandChown
     */
    public function createChownCommand($filePath, $user)
    {
        return new CommandChown($this->resultFactory, $filePath, $user);
    }

    /**
     * @param string $filePath
     * @param string $group
     *
     * @return CommandChgrp
     */
    public function createChgrpCommand($filePath, $group)
    {
        return new CommandChgrp($this->resultFactory, $filePath, $group);
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $archiveFormat
     *
     * @return CommandUnarchive
     */
    public function createUnarchiveCommand($source, $target, $archiveFormat)
    {
        return new CommandUnarchive($this->unarchiverFactory->create($archiveFormat), $source, $target);
    }

    /**
     * @return CommandCollection
     */
    public function createCollection()
    {
        return new CommandCollection($this->resultFactory);
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
        return new CommandMkDir($this->resultFactory, $path, $mode, $recursive);
    }

    /**
     * @param string $path
     *
     * @return CommandRm
     */
    public function createRmCommand($path)
    {
        return new CommandRm($this->resultFactory, $path);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandCopyFile
     */
    public function createCopyFileCommand($sourcePath, $targetPath)
    {
        return new CommandCopyFile($this->resultFactory, $sourcePath, $targetPath);
    }

    /**
     * @param string $targetPath
     * @param string $sourcePath
     *
     * @return CommandSymlink
     */
    public function createSymlinkCommand($targetPath, $sourcePath)
    {
        return new CommandSymlink($this->resultFactory, $targetPath, $sourcePath);
    }
}

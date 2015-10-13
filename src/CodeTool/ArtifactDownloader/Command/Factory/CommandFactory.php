<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Archive\Factory\UnarchiverFactoryInterface;
use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
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
use CodeTool\ArtifactDownloader\Command\CommandRename;
use CodeTool\ArtifactDownloader\Command\CommandRm;
use CodeTool\ArtifactDownloader\Command\CommandSymlink;
use CodeTool\ArtifactDownloader\Command\CommandUnarchive;
use CodeTool\ArtifactDownloader\DirectoryComparator\DirectoryComparatorInterface;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use Psr\Log\LoggerInterface;

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
     * @var DirectoryComparatorInterface
     */
    private $directoryComparator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResultFactoryInterface       $resultFactory
     * @param HttpClientInterface          $httpClient
     * @param UnarchiverFactoryInterface   $unarchiverFactory
     * @param DirectoryComparatorInterface $directoryComparator
     * @param LoggerInterface              $logger
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        HttpClientInterface $httpClient,
        UnarchiverFactoryInterface $unarchiverFactory,
        DirectoryComparatorInterface $directoryComparator,
        LoggerInterface $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->httpClient = $httpClient;
        $this->unarchiverFactory = $unarchiverFactory;
        $this->directoryComparator = $directoryComparator;
        $this->logger = $logger;
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
        return new CommandCollection($this->resultFactory, $this->logger);
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
     * @param string $name
     * @param string $targetPath
     *
     * @return CommandSymlink
     */
    public function createSymlinkCommand($name, $targetPath)
    {
        return new CommandSymlink($this->resultFactory, $name, $targetPath);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return CommandSymlink
     */
    public function createRenameCommand($sourcePath, $targetPath)
    {
        return new CommandRename($this->resultFactory, $sourcePath, $targetPath);
    }

    /**
     * @return CommandNop
     */
    public function createNopCommand()
    {
        return new CommandNop($this->resultFactory);
    }

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
    ) {
        return new CommandCompareDirs(
            $this->directoryComparator,
            $sourcePath,
            $targetPath,
            $onEqualCommand,
            $onNotEqualCommand
        );
    }
}

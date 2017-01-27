<?php

namespace CodeTool\ArtifactDownloader\Command\Factory;

use CodeTool\ArtifactDownloader\Archive\Factory\UnarchiverFactoryInterface;
use CodeTool\ArtifactDownloader\Command\Collection\CommandCollection;
use CodeTool\ArtifactDownloader\Command\CommandCheckFileSignature;
use CodeTool\ArtifactDownloader\Command\CommandCompareDirs;
use CodeTool\ArtifactDownloader\Command\CommandCompareFiles;
use CodeTool\ArtifactDownloader\Command\CommandDownloadFile;
use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Command\CommandNop;
use CodeTool\ArtifactDownloader\Command\CommandUnarchive;
use CodeTool\ArtifactDownloader\Comparator\Directory\DirectoryComparatorInterface;
use CodeTool\ArtifactDownloader\Comparator\File\FileComparatorInterface;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CommandFactory
 *
 * @todo Split to smaller classes by functionality
 * @package CodeTool\ArtifactDownloader\Command\Factory
 */
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
     * @var FileComparatorInterface
     */
    private $fileComparator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResultFactoryInterface       $resultFactory
     * @param HttpClientInterface          $httpClient
     * @param UnarchiverFactoryInterface   $unarchiverFactory
     * @param DirectoryComparatorInterface $directoryComparator
     * @param FileComparatorInterface      $fileComparator
     * @param LoggerInterface              $logger
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        HttpClientInterface $httpClient,
        UnarchiverFactoryInterface $unarchiverFactory,
        DirectoryComparatorInterface $directoryComparator,
        FileComparatorInterface $fileComparator,
        LoggerInterface $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->httpClient = $httpClient;
        $this->unarchiverFactory = $unarchiverFactory;
        $this->directoryComparator = $directoryComparator;
        $this->fileComparator = $fileComparator;
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

    /**
     * @inheritdoc
     */
    public function createCompareFilesCommand(
        $sourcePath,
        $targetPath,
        CommandInterface $onEqualCommand,
        CommandInterface $onNotEqualCommand
    ) {
        return new CommandCompareFiles(
            $this->fileComparator,
            $sourcePath,
            $targetPath,
            $onEqualCommand,
            $onNotEqualCommand
        );
    }
}

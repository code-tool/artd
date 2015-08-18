<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResult;
use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandCheckFileSignature implements CommandInterface
{
    const DEFAULT_ALGORITHM = 'sha256';

    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var string
     */
    private $expectedHash;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $filePath
     * @param string                        $expectedHash
     * @param string                        $algorithm
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
        $filePath,
        $expectedHash,
        $algorithm = self::DEFAULT_ALGORITHM
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->filePath = $filePath;
        $this->algorithm = $algorithm;
        $this->expectedHash = $expectedHash;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        $fileHash = hash_file($this->algorithm, $this->filePath, false);
        if ($fileHash !== $this->expectedHash) {
            return $this->commandResultFactory->createError(
                sprintf(
                    'Invalid "%s" file hash. Expected "%s" got "%s"',
                    $this->filePath,
                    $this->expectedHash,
                    $fileHash
                )
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandCheckFileSignature implements CommandInterface
{
    const DEFAULT_ALGORITHM = 'sha256';

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

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
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $filePath
     * @param string                 $expectedHash
     * @param string                 $algorithm
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        $filePath,
        $expectedHash,
        $algorithm = self::DEFAULT_ALGORITHM
    ) {
        $this->resultFactory = $resultFactory;
        $this->filePath = $filePath;
        $this->algorithm = $algorithm;
        $this->expectedHash = $expectedHash;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $fileHash = hash_file($this->algorithm, $this->filePath, false);
        if ($fileHash !== $this->expectedHash) {
            return $this->resultFactory->createError(
                sprintf(
                    'Invalid "%s" file hash. Expected "%s" got "%s"',
                    $this->filePath,
                    $this->expectedHash,
                    $fileHash
                )
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s(%s) === %s', $this->algorithm, $this->filePath, $this->expectedHash);
    }
}

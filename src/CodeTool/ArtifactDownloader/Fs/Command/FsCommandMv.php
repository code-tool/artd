<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class FsCommandMv implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $targetPath;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $sourcePath
     * @param string                 $targetPath
     */
    public function __construct(ResultFactoryInterface $resultFactory, $sourcePath, $targetPath)
    {
        $this->resultFactory = $resultFactory;
        $this->sourcePath = $sourcePath;
        $this->targetPath = $targetPath;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @rename($this->sourcePath, $this->targetPath)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t move "%s" to "%s"', $this->sourcePath, $this->targetPath)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('mv %s -> %s', $this->sourcePath, $this->targetPath);
    }
}

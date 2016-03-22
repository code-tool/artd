<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

/**
 * Class CommandSetFilePermissions
 *
 * @package CodeTool\ArtifactDownloader\Command
 */
class FsCommandWriteFile implements CommandInterface
{
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
    private $content;

    /**
     * @param ResultFactoryInterface $commandResultFactory
     * @param string                 $filePath
     * @param string                 $content
     */
    public function __construct(ResultFactoryInterface $commandResultFactory, $filePath, $content)
    {
        $this->resultFactory = $commandResultFactory;
        $this->filePath = $filePath;
        $this->content = $content;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === ($fHandle = @fopen($this->filePath, 'w'))) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t open file %s for writing', $this->filePath)
            );
        }

        $writeResult = fwrite($fHandle, $this->content);
        fclose($fHandle);

        if (false === $writeResult) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t write to file %s', $this->filePath)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $contentLength = strlen($this->content);

        $content = $this->content;
        if ($contentLength > 5) {
            $content = substr($this->content, 0, 5);
            $content = str_replace(["\r", "\n"], '', $content) . '...';
        }

        return sprintf('fwrite(%s, %s)', $this->filePath, $content);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

/**
 * Class CommandSetFilePermissions
 *
 * @package CodeTool\ArtifactDownloader\Command
 */
class CommandChmod implements CommandInterface
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
     * @var int|string
     */
    private $mode;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                        $filePath
     * @param string                        $mode
     */
    public function __construct(ResultFactoryInterface $resultFactory, $filePath, $mode)
    {
        $this->resultFactory = $resultFactory;
        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @chmod($this->filePath, $this->mode)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' set permissions "%s" on "%s"', $this->mode, $this->filePath)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('chmod(%s, mode=%o)', $this->filePath, $this->mode);
    }
}

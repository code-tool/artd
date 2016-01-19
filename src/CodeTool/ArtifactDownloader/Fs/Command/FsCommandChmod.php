<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Fs\Util\FsUtilChmodArgParserInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

/**
 * Class CommandSetFilePermissions
 *
 * @package CodeTool\ArtifactDownloader\Fs\Command
 */
class FsCommandChmod implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var FsUtilChmodArgParserInterface
     */
    private $fsUtilChmodArgParser;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var int|string
     */
    private $mode;

    /**
     * @param ResultFactoryInterface        $resultFactory
     * @param FsUtilChmodArgParserInterface $fsUtilChmodArgParser
     * @param string                        $filePath
     * @param string                        $mode
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        FsUtilChmodArgParserInterface $fsUtilChmodArgParser,
        $filePath,
        $mode
    ) {
        $this->resultFactory = $resultFactory;
        $this->fsUtilChmodArgParser = $fsUtilChmodArgParser;

        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $fileInfo = new \SplFileInfo($this->filePath);
        $mode = $this->fsUtilChmodArgParser->parseMode($fileInfo, $this->mode);

        if (false === @chmod($this->filePath, $mode)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' set permissions "%s" on "%s"', $this->mode, $this->filePath)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('chmod(%s, mode=%s)', $this->filePath, $this->mode);
    }
}

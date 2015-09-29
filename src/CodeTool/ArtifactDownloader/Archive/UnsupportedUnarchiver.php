<?php

namespace CodeTool\ArtifactDownloader\Archive;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class UnsupportedUnarchiver implements UnarchiverInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $archiveFormat;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $archiveFormat
     */
    public function __construct(ResultFactoryInterface $resultFactory, $archiveFormat)
    {
        $this->resultFactory = $resultFactory;
        $this->archiveFormat = $archiveFormat;
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return ResultInterface
     */
    public function unarchive($source, $target)
    {
        return $this->resultFactory->createError(sprintf('Unmnown archive format "%s"', $this->archiveFormat));
    }
}

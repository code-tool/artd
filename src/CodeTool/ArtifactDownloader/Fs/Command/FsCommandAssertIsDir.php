<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;

class FsCommandAssertIsDir implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $path;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $path
     */
    public function __construct(ResultFactoryInterface $resultFactory, $path)
    {
        $this->resultFactory = $resultFactory;
        $this->path = $path;
    }

    public function execute()
    {
        if (true !== is_dir($this->path)) {
            $this->resultFactory->createError(sprintf('%s is not a directory', $this->path));
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('assert(true === is_dir(%s))', $this->path);
    }
}

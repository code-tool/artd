<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class FsCommandTouch implements CommandInterface
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
    public function __construct(
        ResultFactoryInterface $resultFactory,
        $path
    ) {
        $this->resultFactory = $resultFactory;
        $this->path = $path;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @touch($this->path)) {
            return $this->resultFactory->createErrorFromGetLast(sprintf(
                'Can\'t touch file "%s"',
                $this->path
            ));
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('touch(%s)', $this->path);
    }
}

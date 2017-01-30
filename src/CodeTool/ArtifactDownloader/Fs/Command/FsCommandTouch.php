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
     * @var string
     */
    private $data;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $path
     * @param string                 $data
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        $path,
        $data
    ) {
        $this->resultFactory = $resultFactory;
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @file_put_contents($this->path, $this->data)) {
            return $this->resultFactory->createErrorFromGetLast(sprintf(
                'Can\'t touch file "%s"',
                $this->path
            ));
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('file_put_contents(%s, %s)', $this->path, $this->data);
    }
}

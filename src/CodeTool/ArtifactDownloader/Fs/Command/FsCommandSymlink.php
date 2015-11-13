<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class FsCommandSymlink implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|string
     */
    private $target;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $name
     * @param string                 $target
     */
    public function __construct(ResultFactoryInterface $resultFactory, $name, $target)
    {
        $this->resultFactory = $resultFactory;
        $this->name = $name;
        $this->target = $target;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (is_link($this->name) && $this->target === readlink($this->name)) {
            return $this->resultFactory->createSuccessful();
        }

        if (false === @symlink($this->target, $this->name)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' create symlink with name "%s" to "%s"', $this->name, $this->target)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ln target=%s name=%s', $this->target, $this->name);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandSymlink implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $target;

    /**
     * @var int|string
     */
    private $source;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $target
     * @param string                 $source
     */
    public function __construct(ResultFactoryInterface $resultFactory, $target, $source)
    {
        $this->resultFactory = $resultFactory;
        $this->target = $target;
        $this->source = $source;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        // todo Handle case when link already exists and valid
        if (false === @symlink($this->source, $this->target)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' create symlink with name "%s" to "%s"', $this->source, $this->target)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('ln path=%s name=%s', $this->source, $this->target);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandRename implements CommandInterface
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
     * @param string                 $source
     * @param string                 $target
     */
    public function __construct(ResultFactoryInterface $resultFactory, $source, $target)
    {
        $this->resultFactory = $resultFactory;
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @rename($this->source, $this->target)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' rename "%s" to "%s"', $this->source, $this->target)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('rename %s -> %s', $this->source, $this->target);
    }
}

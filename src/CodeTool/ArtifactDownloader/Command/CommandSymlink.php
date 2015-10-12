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
    private $name;

    /**
     * @var int|string
     */
    private $source;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $name
     * @param string                 $source
     */
    public function __construct(ResultFactoryInterface $resultFactory, $name, $source)
    {
        $this->resultFactory = $resultFactory;
        $this->name = $name;
        $this->source = $source;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (is_link($this->name) && $this->source === readlink($this->name)) {
            return $this->resultFactory->createSuccessful();
        }

        if (false === @symlink($this->source, $this->name)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' create symlink with name "%s" to "%s"', $this->name, $this->source)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('ln path=%s name=%s', $this->source, $this->name);
    }
}

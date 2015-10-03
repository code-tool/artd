<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\DirectoryComparator\DirectoryComparatorInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandCompareDirs implements CommandInterface
{
    /**
     * @var DirectoryComparatorInterface
     */
    private $directoryComparator;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $target;

    /**
     * @var CommandInterface
     */
    private $onEqualCommand;

    /**
     * @var CommandInterface
     */
    private $onNotEqualCommand;

    public function __construct(
        DirectoryComparatorInterface $directoryComparator,
        $source,
        $target,
        CommandInterface $onEqualCommand,
        CommandInterface $onNotEqualCommand
    ) {
        $this->directoryComparator = $directoryComparator;

        $this->source = $source;
        $this->target = $target;

        $this->onEqualCommand = $onEqualCommand;
        $this->onNotEqualCommand = $onNotEqualCommand;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === $this->directoryComparator->isEqual($this->source, $this->target, 'simple')) {
            return $this->onNotEqualCommand->execute();
        }

        return $this->onEqualCommand->execute();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Compare %s with %s', $this->source, $this->target);
    }
}

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

    private function preFormat($str)
    {
        $str = str_replace("\t", '', $str);
        return  "\t\t" . str_replace(PHP_EOL, PHP_EOL . "\t\t", $str);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = sprintf('compare %s with %s', $this->source, $this->target);

        $result .= PHP_EOL . sprintf(
            "\t - on equal -> %s%s\t",
            PHP_EOL,
            $this->preformat($this->onEqualCommand)
        );
        $result .= PHP_EOL . sprintf(
            "\t - on NOT equal -> %s%s\t",
            PHP_EOL,
            $this->preformat($this->onNotEqualCommand)
        );

        return $result;
    }
}

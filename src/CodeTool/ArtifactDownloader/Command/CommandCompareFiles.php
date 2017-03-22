<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Comparator\File\FileComparatorInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandCompareFiles implements CommandInterface
{
    /**
     * @var FileComparatorInterface
     */
    private $fileComparator;

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
        FileComparatorInterface $fileComparator,
        $source,
        $target,
        CommandInterface $onEqualCommand,
        CommandInterface $onNotEqualCommand
    ) {
        $this->fileComparator = $fileComparator;

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
        if (false === $this->fileComparator->isEqual($this->source, $this->target, 'simple')) {
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
            $this->preFormat($this->onEqualCommand)
        );
        $result .= PHP_EOL . sprintf(
            "\t - on NOT equal -> %s%s\t",
            PHP_EOL,
            $this->preFormat($this->onNotEqualCommand)
        );

        return $result;
    }
}

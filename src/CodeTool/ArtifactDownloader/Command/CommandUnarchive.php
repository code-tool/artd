<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Archive\UnarchiverInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandUnarchive implements CommandInterface
{
    /**
     * @var UnarchiverInterface
     */
    private $unarchiver;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $target;

    /**
     * @param UnarchiverInterface $unarchiver
     * @param string              $source
     * @param string              $target
     */
    public function __construct(UnarchiverInterface $unarchiver, $source, $target)
    {
        $this->unarchiver = $unarchiver;
        //
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->unarchiver->unarchive($this->source, $this->target);
    }

    public function __toString()
    {
        return sprintf('unarchive %s -> %s', $this->source, $this->target);
    }
}

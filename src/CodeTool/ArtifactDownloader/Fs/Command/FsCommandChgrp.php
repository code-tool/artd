<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class FsCommandChgrp implements CommandInterface
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
     * @var string
     */
    private $group;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $target
     * @param string                 $group
     */
    public function __construct(ResultFactoryInterface $resultFactory, $target, $group)
    {
        $this->resultFactory = $resultFactory;

        $this->target = $target;
        $this->group = $group;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @chgrp($this->target, $this->group)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t chgrp "%s" to "%s"', $this->target, $this->group)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('chgrp(%s, %s)', $this->target, $this->group);
    }
}

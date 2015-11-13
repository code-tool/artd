<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class FsCommandChown implements CommandInterface
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
    private $user;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                        $target
     * @param string                        $user
     */
    public function __construct(ResultFactoryInterface $resultFactory, $target, $user)
    {
        $this->resultFactory = $resultFactory;

        $this->target = $target;
        $this->user = $user;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @chown($this->target, $this->user)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t chown "%s" to "%s"', $this->target, $this->user)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('chown(%s, %s)', $this->target, $this->user);
    }
}

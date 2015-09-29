<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandMkDir implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $mode;

    /**
     * @var bool
     */
    private $recursive;

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param string                 $path
     * @param int                    $mode
     * @param bool                   $recursive
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        $path,
        $mode = 0777,
        $recursive = false
    ) {
        $this->resultFactory = $resultFactory;
        $this->path = $path;
        $this->mode = $mode;
        $this->recursive = $recursive;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === @mkdir($this->path, $this->mode, $this->recursive)) {
            return $this->resultFactory->createErrorFromGetLast(sprintf(
                'Can\'t create dir "%s" (%o recursive=%s)',
                $this->path,
                $this->mode,
                $this->recursive ? 't' : 'f'
            ));
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('%s: mkdir(%s, %s, %s)', __CLASS__, $this->path, $this->mode, $this->recursive ? 't': 'f');
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandMkDir implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

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
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $path
     * @param int                           $mode
     * @param bool                          $recursive
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
        $path,
        $mode = 0777,
        $recursive = false
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->path = $path;
        $this->mode = $mode;
        $this->recursive = $recursive;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === @mkdir($this->path, $this->mode, $this->recursive)) {
            return $this->commandResultFactory->createErrorFromGetLast(sprintf(
                'Can\'t create dir "%s" (%o recursive=%s)',
                $this->path,
                $this->mode,
                $this->recursive ? 't' : 'f'
            ));
        }

        return $this->commandResultFactory->createSuccess();
    }
}

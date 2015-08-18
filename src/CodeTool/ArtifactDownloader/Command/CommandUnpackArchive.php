<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandUnpackArchive implements CommandInterface
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
     * @var string
     */
    private $target;

    /**
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param string                        $path
     * @param string                        $target
     */
    public function __construct(CommandResultFactoryInterface $commandResultFactory, $path, $target)
    {
        $this->commandResultFactory = $commandResultFactory;
        $this->path = $path;
        $this->target = $target;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        try {
            $phar = new \PharData($this->path);
            $phar->extractTo($this->target);
        } catch (\Exception $e) {
           return $this->commandResultFactory->createErrorFromException($e);
        }

        return $this->commandResultFactory->createSuccess();
    }
}

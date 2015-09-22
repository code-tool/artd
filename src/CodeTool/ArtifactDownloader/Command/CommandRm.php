<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;

class CommandRm implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var string
     */
    private $path;

    public function __construct(CommandResultFactoryInterface $commandResultFactory, $path)
    {
        $this->commandResultFactory = $commandResultFactory;
        $this->path = $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function doRm($path)
    {
        if (is_file($path)) {
            return @unlink($path);
        }

        foreach (scandir($path) as $object) {
            if ('.' === $object || '..' === $object) {
                continue;
            }

            if (false === $this->doRm($path . DIRECTORY_SEPARATOR . $object)) {
                return false;
            }
        }

        return @rmdir($path);
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (false === $this->doRm($this->path)) {
            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\' delete path "%s.', $this->path)
            );
        }

        return $this->commandResultFactory->createSuccess();
    }
}

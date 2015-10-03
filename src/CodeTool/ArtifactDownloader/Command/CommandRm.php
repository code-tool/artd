<?php

namespace CodeTool\ArtifactDownloader\Command;


use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandRm implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $path;

    public function __construct(ResultFactoryInterface $resultFactory, $path)
    {
        $this->resultFactory = $resultFactory;
        $this->path = $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function doRm($path)
    {
        if (is_file($path) || is_link($path)) {
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
     * @return ResultInterface
     */
    public function execute()
    {
        if (false === $this->doRm($this->path)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' delete path "%s.', $this->path)
            );
        }

        return $this->resultFactory->createSuccessful();
    }

    public function __toString()
    {
        return sprintf('rm %s', $this->path);
    }
}

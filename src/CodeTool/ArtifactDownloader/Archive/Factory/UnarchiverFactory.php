<?php

namespace CodeTool\ArtifactDownloader\Archive\Factory;

use CodeTool\ArtifactDownloader\Archive\TarUnarchiver;
use CodeTool\ArtifactDownloader\Util\BasicUtil;
use CodeTool\ArtifactDownloader\Util\CmdRunner\CmdRunnerInterface;

class UnarchiverFactory
{
    private $cmdRunner;

    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var
     */
    private $tarCmdPath;

    /**
     * @param BasicUtil $basicUtil
     */
    public function __construct(CmdRunnerInterface $cmdRunner, BasicUtil $basicUtil)
    {
        $this->cmdRunner = $cmdRunner;
        $this->basicUtil = $basicUtil;
    }

    private function getTarCmdPath()
    {
        if (null === $this->tarCmdPath) {
            if (null === ($this->tarCmdPath = $this->basicUtil->getBinPath('gtar'))) {
                if (null === ($this->tarCmdPath = $this->basicUtil->getBinPath('tar'))) {
                    throw new \RuntimeException('Cant find tar or gtar binary');
                }
            }
        }

        return $this->tarCmdPath;
    }

    public function createForTar($compressType)
    {
        return new TarUnarchiver($this->cmdRunner, $this->getTarCmdPath(), $compressType);
    }
}

<?php

namespace CodeTool\ArtifactDownloader\CmdRunner;

use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface;

interface CmdRunnerInterface
{
    /**
     * @param string $cmd
     *
     * @return CmdRunnerResultInterface
     */
    public function run($cmd);
}

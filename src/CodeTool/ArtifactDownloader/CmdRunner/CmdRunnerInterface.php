<?php

namespace CodeTool\ArtifactDownloader\CmdRunner;

use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface;

interface CmdRunnerInterface
{
    /**
     * @param string   $cmd
     * @param string   $cwd
     * @param string[] $env
     *
     * @return CmdRunnerResultInterface
     */
    public function run($cmd, $cwd = null, $env = null);
}

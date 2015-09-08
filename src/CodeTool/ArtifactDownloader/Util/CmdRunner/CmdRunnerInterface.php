<?php

namespace CodeTool\ArtifactDownloader\Util\CmdRunner;

use CodeTool\ArtifactDownloader\Util\CmdRunner\Result\CmdRunnerResultInterface;

interface CmdRunnerInterface
{
    /**
     * @param string $cmd
     *
     * @return CmdRunnerResultInterface
     */
    public function run($cmd);
}

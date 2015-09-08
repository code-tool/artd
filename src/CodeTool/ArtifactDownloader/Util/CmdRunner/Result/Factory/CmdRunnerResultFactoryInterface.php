<?php

namespace CodeTool\ArtifactDownloader\Util\CmdRunner\Result\Factory;

use CodeTool\ArtifactDownloader\Util\CmdRunner\Result\CmdRunnerResultInterface;

interface CmdRunnerResultFactoryInterface
{
    /**
     * @param int         $exitCode
     * @param string|null $stdOut
     * @param string|null $stdErr
     *
     * @return CmdRunnerResultInterface
     */
    public function make($exitCode, $stdOut, $stdErr);
}

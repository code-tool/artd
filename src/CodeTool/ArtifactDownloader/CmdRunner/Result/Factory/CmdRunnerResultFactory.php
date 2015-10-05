<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Result\Factory;

use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResult;
use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface;

class CmdRunnerResultFactory implements CmdRunnerResultFactoryInterface
{
    /**
     * @param int         $exitCode
     * @param string|null $stdOut
     * @param string|null $stdErr
     *
     * @return CmdRunnerResultInterface
     */
    public function make($exitCode, $stdOut, $stdErr)
    {
        return new CmdRunnerResult($exitCode, $stdOut, $stdErr);
    }
}

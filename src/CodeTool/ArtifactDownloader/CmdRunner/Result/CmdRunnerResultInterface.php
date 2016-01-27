<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Result;

use CodeTool\ArtifactDownloader\Result\ResultInterface;

interface CmdRunnerResultInterface extends ResultInterface
{
    /**
     * @return int
     */
    public function getExitCode();

    /**
     * @return null|string
     */
    public function getStdOut();

    /**
     * @return null|string
     */
    public function getStdErr();
}

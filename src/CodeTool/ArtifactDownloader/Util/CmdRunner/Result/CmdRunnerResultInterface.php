<?php

namespace CodeTool\ArtifactDownloader\Util\CmdRunner\Result;

interface CmdRunnerResultInterface
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

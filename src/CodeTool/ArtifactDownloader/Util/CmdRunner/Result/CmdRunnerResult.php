<?php

namespace CodeTool\ArtifactDownloader\Util\CmdRunner\Result;

class CmdRunnerResult implements CmdRunnerResultInterface
{
    /**
     * @var int
     */
    private $exitCode;

    /**
     * @var null|string
     */
    private $stdOut;

    /**
     * @var null|string
     */
    private $stdErr;

    /**
     * @param int         $exitCode
     * @param string|null $stdOut
     * @param string|null $stdErr
     */
    public function __construct($exitCode, $stdOut, $stdErr)
    {
        $this->exitCode = $exitCode;
        $this->stdOut = $stdOut;
        $this->stdErr = $stdErr;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return null|string
     */
    public function getStdOut()
    {
        return $this->stdOut;
    }

    /**
     * @return null|string
     */
    public function getStdErr()
    {
        return $this->stdErr;
    }
}

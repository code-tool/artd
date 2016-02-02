<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Result\Factory;

use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResult;
use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;

class CmdRunnerResultFactory implements CmdRunnerResultFactoryInterface
{
    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    /**
     * @param ErrorFactoryInterface $errorFactory
     */
    public function __construct(ErrorFactoryInterface $errorFactory)
    {
        $this->errorFactory = $errorFactory;
    }

    /**
     * @param int         $exitCode
     * @param string|null $stdOut
     * @param string|null $stdErr
     *
     * @return CmdRunnerResultInterface
     */
    public function make($exitCode, $stdOut, $stdErr)
    {
        $error = null;
        if ($exitCode !== 0) {
            if ('' === $errorMessage = trim($stdErr)) {
                $errorMessage = trim($stdOut);
            }

            $error = $this->errorFactory->create($errorMessage);
        }

        return new CmdRunnerResult($exitCode, $stdOut, $stdErr, $error);
    }

    /**
     * @return CmdRunnerResultInterface
     */
    public function makeFromGetLast()
    {
        return new CmdRunnerResult(-1, null, null, $this->errorFactory->createFromGetLast('proc_open: '));
    }
}

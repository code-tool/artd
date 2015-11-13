<?php

namespace CodeTool\ArtifactDownloader\Runit;

use CodeTool\ArtifactDownloader\CmdRunner\CmdRunnerInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class Runit implements RunitInterface
{
    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var string
     */
    private $svPath;

    /**
     * @param BasicUtil              $basicUtil
     * @param CmdRunnerInterface     $cmdRunner
     * @param ResultFactoryInterface $resultFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        CmdRunnerInterface $cmdRunner,
        ResultFactoryInterface $resultFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->cmdRunner = $cmdRunner;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return null|string
     */
    private function getSvPath()
    {
        if (null === $this->svPath) {
            $this->svPath = $this->basicUtil->getBinPath('sv');

            if (null === $this->svPath) {
                throw new \RuntimeException(sprintf('Can\'t find sv binary. Does runit installed?'));
            }
        }

        return $this->svPath;
    }

    /**
     * @param string $serviceName
     * @param string $command
     *
     * @return \CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface
     */
    private function execSvCommand($serviceName, $command)
    {
        return $this->cmdRunner->run(sprintf('%s %s %s', $this->getSvPath(), $command, $serviceName));
    }

    /**
     * @param string $haystack
     * @param int    $offset
     * @param string $symbol
     *
     * @return string
     */
    private function getStrBeforeSymbol($haystack, $offset = 0, $symbol = ':')
    {
        return substr($haystack, $offset, strpos($haystack, $symbol, $offset) - $offset);
    }

    /**
     * @param string $name
     * @param string $command
     * @param string $expectedStatus
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    private function execSvCommandAndCheckResult($name, $command, $expectedStatus)
    {
        $runResult = $this->execSvCommand($name, $command);
        if (0 === $runResult->getExitCode() &&
            // line like 'ok: down:'
            $expectedStatus === $this->getStrBeforeSymbol($runResult->getStdOut(), strlen('ok: '))
        ) {
            return $this->resultFactory->createSuccessful();
        }

        return $this->resultFactory->createError($runResult->getStdOut());
    }

    /**
     * @param string $name
     * @param string $state
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function setServiceState($name, $state)
    {
        $runResult = $this->execSvCommand($name, 'status');
        if (0 !== $runResult->getExitCode()) {
            return $this->resultFactory->createError($runResult->getStdOut());
        }
        $initialStatus = $this->getStrBeforeSymbol($runResult->getStdOut());

        if ('run' === $initialStatus && self::STATE_STOPPED === $state) {
            return $this->execSvCommandAndCheckResult($name, 'stop', 'down');
        }

        if (self::STATE_RESTARTED === $state) {
            return $this->execSvCommandAndCheckResult($name, 'restart', 'run');
        }

        if ('run' !== $initialStatus && self::STATE_STARTED === $state) {
            return $this->execSvCommandAndCheckResult($name, 'start', 'run');
        }

        return $this->resultFactory->createSuccessful();
    }
}

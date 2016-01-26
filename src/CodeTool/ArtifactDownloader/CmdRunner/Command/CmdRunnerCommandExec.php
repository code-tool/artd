<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Command;

use CodeTool\ArtifactDownloader\CmdRunner\CmdRunnerInterface;
use CodeTool\ArtifactDownloader\Command\CommandInterface;

class CmdRunnerCommandExec implements CommandInterface
{
    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @var string
     */
    private $cmd;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string[]
     */
    private $env;

    /**
     * @param CmdRunnerInterface $cmdRunner
     * @param string             $cmd
     * @param string             $cwd
     * @param string[]           $env
     */
    public function __construct(
        CmdRunnerInterface $cmdRunner,
        $cmd,
        $cwd,
        array $env = []
    ) {
        $this->cmdRunner = $cmdRunner;
        //
        $this->cmd = $cmd;
        $this->cwd = $cwd;
        //
        $this->env = $env;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->cmdRunner->run($this->cmd, $this->cwd, $this->env);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $envStr = '';
        foreach ($this->env as $name => $value) {
            $envStr .= sprintf('%s=%s ');
        }

        return sprintf('%s%s', $envStr, $this->cmd);
    }
}

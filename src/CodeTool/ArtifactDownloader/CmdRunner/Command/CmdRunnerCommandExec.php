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
        static $suppressKeys = [
            'HISTSIZE', 'LS_COLORS', 'PS1', 'SUDO_COMMAND', 'MAIL',
            'SUDO_USER', 'SUDO_UID', 'SUDO_GID', 'SSH_AUTH_SOCK'
        ];

        $envStr = '';
        $suppressed = false;
        foreach ($this->env as $name => $value) {
            if (in_array($name, $suppressKeys, true)) {
                $suppressed = true;
                continue;
            }
            $envStr .= sprintf('%s=%s ', $name, $value);
        }
        if ($suppressed) {
            $envStr = '[suppressed] ' . $envStr;
        }

        return sprintf('cd %s; %s%s', $this->cwd, $envStr, $this->cmd);
    }
}

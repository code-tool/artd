<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Command\Factory;

use CodeTool\ArtifactDownloader\CmdRunner\CmdRunnerInterface;
use CodeTool\ArtifactDownloader\CmdRunner\Command\CmdRunnerCommandExec;

class CmdRunnerCommandFactory implements CmdRunnerCommandFactoryInterface
{
    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @param CmdRunnerInterface $cmdRunner
     */
    public function __construct(CmdRunnerInterface $cmdRunner)
    {
        $this->cmdRunner = $cmdRunner;
    }

    private function getCurrentEnv()
    {
        if ([] !== $_ENV) {
            return $_ENV;
        }

        $forbiddenKeys = [
            'PHP_SELF', 'argv', 'argc', 'GATEWAY_INTERFACE',
            'SERVER_ADDR', 'SERVER_NAME', 'SERVER_SOFTWARE', 'SERVER_PROTOCOL', 'REQUEST_METHOD', 'REQUEST_TIME',
            'REQUEST_TIME_FLOAT', 'QUERY_STRING', 'DOCUMENT_ROOT',
            'HTTP_ACCEPT', 'HTTP_ACCEPT_CHARSET', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION', 'HTTP_HOST', 'HTTP_REFERER', 'HTTP_USER_AGENT', 'HTTPS',
            'REMOTE_ADDR', 'REMOTE_HOST', 'REMOTE_PORT', 'REMOTE_USER', 'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME', 'SERVER_ADMIN', 'SERVER_PORT', 'SERVER_SIGNATURE',
            'PATH_TRANSLATED', 'SCRIPT_NAME', 'REQUEST_URI', 'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER', 'PHP_AUTH_PW', 'AUTH_TYPE', 'PATH_INFO', 'ORIG_PATH_INFO'
        ];

        $result = [];
        foreach ($_SERVER as $k => $value) {
            if (in_array($k, $forbiddenKeys, true)) {
                continue;
            }

            if (false === is_string($value)) {
                continue;
            }

            $result[$k] = $value;
        }

        return $result;
    }

    /**
     * @param bool     $clearEnv
     * @param string[] $env
     *
     * @return string[]
     */
    private function getExecEnv($clearEnv = false, array $env = [])
    {
        if (true === $clearEnv) {
            return $env;
        }

        return array_merge($this->getCurrentEnv(), $env);
    }

    /**
     * @param string   $cmd
     * @param string   $cwd
     * @param bool     $clearEnv
     * @param string[] $env
     *
     * @return CmdRunnerCommandExec
     */
    public function createExecCommand($cmd, $cwd, $clearEnv = false, array $env = [])
    {
        return new CmdRunnerCommandExec($this->cmdRunner, $cmd, $cwd, $this->getExecEnv($clearEnv, $env));
    }
}

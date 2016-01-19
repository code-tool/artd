<?php

namespace CodeTool\ArtifactDownloader\Fs\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Fs\Util\FsUtilChmodArgParserInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

/**
 * Class CommandSetFilePermissions
 *
 * @package CodeTool\ArtifactDownloader\Command
 */
class FsCommandPermissions implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var FsUtilChmodArgParserInterface
     */
    private $fsUtilChmodArgParser;

    /**
     * @var string
     */
    private $path;

    /**
     * @var null|int|string
     */
    private $mode;

    /**
     * @var int|null
     */
    private $uid;

    /**
     * @var int|null
     */
    private $gid;

    /**
     * @var bool|false
     */
    private $recursive;

    /**
     * @param ResultFactoryInterface        $commandResultFactory
     * @param FsUtilChmodArgParserInterface $fsUtilChmodArgParser
     * @param string                        $path
     * @param int|null                      $uid
     * @param int|null                      $gid
     * @param int|string                    $mode
     * @param bool                          $recursive
     */
    public function __construct(
        ResultFactoryInterface $commandResultFactory,
        FsUtilChmodArgParserInterface $fsUtilChmodArgParser,
        $path,
        $uid,
        $gid,
        $mode,
        $recursive = false
    ) {
        $this->resultFactory = $commandResultFactory;
        $this->fsUtilChmodArgParser = $fsUtilChmodArgParser;
        //
        $this->path = $path;
        //
        $this->uid = $uid;
        $this->gid = $gid;
        $this->mode = $mode;
        //
        $this->recursive = $recursive;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param string|int   $mode
     *
     * @return ResultInterface|null
     */
    private function chmodOrError(\SplFileInfo $fileInfo, $mode)
    {
        $parsedMode = $this->fsUtilChmodArgParser->parseMode($fileInfo, $mode);
        if (($fileInfo->getPerms() & FsUtilChmodArgParserInterface::PERM_BITS) === $parsedMode) {
            return null;
        }

        if (false === @chmod($fileInfo->getPathname(), $parsedMode)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\' set permissions "%s" on "%s"', $mode, $fileInfo->getPathname())
            );
        }

        return null;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param int          $user
     *
     * @return ResultInterface|null
     */
    private function chownOrError(\SplFileInfo $fileInfo, $user)
    {
        if ($fileInfo->getOwner() === $user) {
            return null;
        }

        if (false === @chown($fileInfo->getPathname(), $user)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t chown "%s" to "%s"', $fileInfo->getPathname(), $user)
            );
        }

        return null;
    }

    private function chgrpOrError(\SplFileInfo $fileInfo, $group)
    {
        if ($fileInfo->getGroup() === $group) {
            return null;
        }

        if (false === @chgrp($fileInfo->getPathname(), $group)) {
            return $this->resultFactory->createErrorFromGetLast(
                sprintf('Can\'t chgrp "%s" to "%s"', $fileInfo->getPathname(), $group)
            );
        }

        return null;
    }

    /**
     * @param string     $path
     * @param bool|false $reverseOrder
     *
     * @return \SplFileInfo[]
     */
    private function getFileIterator($path, $reverseOrder = false)
    {
        if (is_file($path)) {
            return new \ArrayIterator([new \SplFileInfo($path)]);
        }

        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            $reverseOrder === true ? \RecursiveIteratorIterator::CHILD_FIRST : \RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * @param \SplFileInfo $fileInfo
     *
     * @return ResultInterface|null
     */
    private function doChange(\SplFileInfo $fileInfo)
    {
        if ($fileInfo->isLink()) {
            return null;
        }

        if (null !== $this->mode && (null !== ($error = $this->chmodOrError($fileInfo, $this->mode)))) {
            return $error;
        }

        if (null !== $this->gid && (null !== ($error = $this->chgrpOrError($fileInfo, $this->gid)))) {
            return $error;
        }

        if (null !== $this->uid && (null !== ($error = $this->chownOrError($fileInfo, $this->uid)))) {
            return $error;
        }

        return null;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (true === $this->recursive && false === is_file($this->path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $splObjectInfo) {
                if (null !== ($error = $this->doChange($splObjectInfo))) {
                    return $error;
                }
            }
        }

        $fileInfo = new \SplFileInfo($this->path);

        if (null !== ($error = $this->doChange($fileInfo))) {
            return $error;
        }

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $flags = [];
        if (null !== $this->uid) {
            $flags[] = sprintf('uid=%d', $this->uid);
        }

        if (null !== $this->gid) {
            $flags[] = sprintf('gid=%d', $this->gid);
        }

        if (null !== $this->mode) {
            $flags[] = sprintf('mode=%s', $this->mode);
        }

        $flags[] = sprintf('recursive=%s', $this->recursive ? 't' : 'f');

        return sprintf('permissions(%s)', implode(', ', $flags));
    }
}

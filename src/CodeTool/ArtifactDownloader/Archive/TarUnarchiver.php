<?php

namespace CodeTool\ArtifactDownloader\Archive;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Util\CmdRunner\CmdRunnerInterface;

class TarUnarchiver implements UnarchiverInterface
{
    const COMPRESS_TYPE_GZ = 'z';

    const COMPRESS_TYPE_NONE = '';

    const COMPRESS_TYPE_BZIP2 = 'j';

    const COMPRESS_TYPE_XZ = 'J';

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @var string
     */
    private $tarCmdPath;

    /**
     * @var string
     */
    private $compressType = 'z';

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param CmdRunnerInterface     $cmdRunner
     * @param string                 $tarCmdPath
     * @param string                 $compressType
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        CmdRunnerInterface $cmdRunner,
        $tarCmdPath,
        $compressType = self::COMPRESS_TYPE_GZ
    ) {
        $this->resultFactory = $resultFactory;
        $this->cmdRunner = $cmdRunner;
        $this->tarCmdPath = $tarCmdPath;
        $this->compressType = $compressType;
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return string
     */
    private function buildCmd($source, $target)
    {
        return sprintf('%s -C "%s" -x%sf "%s"', $this->tarCmdPath, $target, $this->compressType, $source);
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return ResultInterface
     */
    public function unarchive($source, $target)
    {
        if (null === $this->tarCmdPath) {
            return $this->resultFactory->createError('Can\'t find tag or gtar', $this);
        }

        $runResult = $this->cmdRunner->run($this->buildCmd($source, $target));

        if (0 !== $runResult->getExitCode()) {
            return $this->resultFactory->createError($runResult->getStdErr());
        }

        return $this->resultFactory->createSuccessful();
    }
}

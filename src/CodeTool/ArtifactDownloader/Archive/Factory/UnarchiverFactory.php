<?php

namespace CodeTool\ArtifactDownloader\Archive\Factory;

use CodeTool\ArtifactDownloader\Archive\ArchiveType;
use CodeTool\ArtifactDownloader\Archive\TarUnarchiver;
use CodeTool\ArtifactDownloader\Archive\UnarchiverInterface;
use CodeTool\ArtifactDownloader\Archive\UnsupportedUnarchiver;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;
use CodeTool\ArtifactDownloader\Util\CmdRunner\CmdRunnerInterface;

class UnarchiverFactory implements UnarchiverFactoryInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var string|null
     */
    private $tarCmdPath;

    public function __construct(
        ResultFactoryInterface $resultFactory,
        CmdRunnerInterface $cmdRunner,
        BasicUtil $basicUtil
    ) {
        $this->resultFactory = $resultFactory;
        $this->cmdRunner = $cmdRunner;
        $this->basicUtil = $basicUtil;
    }

    /**
     * @return null|string
     */
    private function doGetGtarOrTarPath()
    {
        if (null !== ($result = $this->basicUtil->getBinPath('gtar'))) {
            return $result;
        }

        if (null !== ($result = $this->basicUtil->getBinPath('tar'))) {
            return $result;
        }

        return null;
    }

    /**
     * @return null|string
     */
    private function getTarCmdPath()
    {
        if (null === $this->tarCmdPath) {
            $this->tarCmdPath = $this->doGetGtarOrTarPath();
        }

        return $this->tarCmdPath;
    }

    /**
     * @param string $compressType
     *
     * @return TarUnarchiver
     */
    private function createTar($compressType)
    {
        return new TarUnarchiver($this->resultFactory, $this->cmdRunner, $this->getTarCmdPath(), $compressType);
    }

    /**
     * @param string $archiveType
     *
     * @return UnarchiverInterface
     */
    public function create($archiveType)
    {
        switch ($archiveType) {
            case ArchiveType::TAR:
                $result = $this->createTar(TarUnarchiver::COMPRESS_TYPE_NONE);
                break;
            case ArchiveType::TAR_GZ:
                $result = $this->createTar(TarUnarchiver::COMPRESS_TYPE_GZ);
                break;
            case ArchiveType::TAR_XZ:
                $result = $this->createTar(TarUnarchiver::COMPRESS_TYPE_XZ);
                break;
            case ArchiveType::TAR_BZIP2:
                $result = $this->createTar(TarUnarchiver::COMPRESS_TYPE_BZIP2);
                break;
            default:
                $result = new UnsupportedUnarchiver($this->resultFactory, $archiveType);
        }

        return $result;
    }
}

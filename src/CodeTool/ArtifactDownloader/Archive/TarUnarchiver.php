<?php

namespace CodeTool\ArtifactDownloader\Archive;

use CodeTool\ArtifactDownloader\Util\CmdRunner\CmdRunnerInterface;

class TarUnarchiver
{
    const COMPRESS_TYPE_GZ = 'z';

    const COMPRESS_TYPE_NONE = '';

    const COMPRESS_TYPE_BZIP2 = 'j';

    const COMPRESS_TYPE_XZ = 'J';

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
     * @param CmdRunnerInterface $cmdRunner
     * @param string             $tarCmdPath
     * @param string             $compressType
     */
    public function __construct(CmdRunnerInterface $cmdRunner, $tarCmdPath, $compressType = self::COMPRESS_TYPE_GZ)
    {
        $this->cmdRunner = $cmdRunner;
        $this->tarCmdPath = $tarCmdPath;
        $this->compressType = $compressType;
    }

    public function getFilesInArchive($path)
    {
        $crd = sprintf('%s -t%sf "%s"', $this->tarCmdPath, $this->compressType, $path);
    }

    public function unarchive()
    {
        /*
         * cmd = '%s -x%sf "%s"' % (self.cmd_path, self.zipflag, self.src)
        rc, out, err = self.module.run_command(cmd, cwd=self.dest)
        return dict(cmd=cmd, rc=rc, out=out, err=err)
         */
    }

    public function isAnarchived($mode, $owner, $group)
    {
        //
        //
    }
}

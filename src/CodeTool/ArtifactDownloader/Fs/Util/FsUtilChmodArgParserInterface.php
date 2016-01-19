<?php

namespace CodeTool\ArtifactDownloader\Fs\Util;


interface FsUtilChmodArgParserInterface
{
    const PERM_BITS = 07777;

    const EXEC_PERM_BITS = 00111;

    const S_ISUID = 04000;

    const S_ISGID = 02000;

    const S_ENFMT = self::S_ISGID;

    const S_ISVTX = 01000;

    const S_IREAD = 00400;

    const S_IWRITE = 00200;

    const S_IEXEC = 00100;

    const S_IRWXU = 00700;

    const S_IRUSR = 00400;

    const S_IWUSR = 00200;

    const S_IXUSR = 00100;

    const S_IRWXG = 00070;

    const S_IRGRP = 00040;

    const S_IWGRP = 00020;

    const S_IXGRP = 00010;

    const S_IRWXO = 00007;

    const S_IROTH = 00004;

    const S_IWOTH = 00002;

    const S_IXOTH = 00001;

    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $mode
     *
     * @return int
     */
    public function parseMode(\SplFileInfo $fileInfo, $mode);
}

<?php

namespace CodeTool\ArtifactDownloader\Util;

class BasicUtil
{
    /**
     * @param string $binName
     *
     * @return null|string
     */
    public function getBinPath($binName)
    {
        $sbinPaths = ['/sbin', '/usr/sbin', '/usr/local/sbin'];

        $paths = explode(PATH_SEPARATOR, getenv('PATH'));

        foreach ($sbinPaths as $path) {
            $paths[] = $path;
        }

        foreach ($paths as $path) {
            $binPath = $path . DIRECTORY_SEPARATOR . $binName;
            if (file_exists($binPath) && is_executable($binPath)) {
                return $binPath;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        return
            sys_get_temp_dir() .
            DIRECTORY_SEPARATOR .
            'artifact-downloader-' .
            posix_getpid() .
            '-' .
            uniqid('', true);
    }
}

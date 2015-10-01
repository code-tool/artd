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

    public function getRandomStr($length, $keySpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keySpace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keySpace[mt_rand(0, $max)];
        }

        return $str;
    }

    public function getTmpName()
    {
        return 'artifact-downloader-' . posix_getpid() . '-' . $this->getRandomStr(5);
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getTmpName();
    }

    public function getRelativeTmpPath($path)
    {
        return realpath($path . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . $this->getTmpName();
    }
}

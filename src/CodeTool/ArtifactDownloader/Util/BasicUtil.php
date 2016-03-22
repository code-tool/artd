<?php

namespace CodeTool\ArtifactDownloader\Util;

class BasicUtil
{
    const TMP_NAME_DEFAULT_PREFIX = 'artd-';

    const TMP_NAME_DEFAULT_POSTFIX = '';

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

    public function getTmpName($prefix = self::TMP_NAME_DEFAULT_PREFIX, $postfix = self::TMP_NAME_DEFAULT_POSTFIX)
    {
        return $prefix . posix_getpid() . '-' . $this->getRandomStr(5) . $postfix;
    }

    /**
     * @param string $prefix
     * @param string $postfix
     *
     * @return string
     */
    public function getTmpPath($prefix = self::TMP_NAME_DEFAULT_PREFIX, $postfix = self::TMP_NAME_DEFAULT_POSTFIX)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getTmpName($prefix, $postfix);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getRelativeTmpPath($path)
    {
        return dirname($path) . DIRECTORY_SEPARATOR . $this->getTmpName();
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    public function isSourceLocal($source)
    {
        return false === strpos($source, '://');
    }
}

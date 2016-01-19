<?php

namespace CodeTool\ArtifactDownloader\Fs\Util;

class FsUtilPermissionStrParser implements FsUtilPermissionStrParserInterface
{
    const EXPR = '/^(recursive|user|group|mode)=(.+?)$/';

    /**
     * @param string $value
     *
     * @return bool
     */
    private function parseRecursiveValue($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param string $value
     *
     * @return null|int
     */
    private function parseUserValue($value)
    {
        if (is_numeric($value) && false !== posix_getpwuid($value)) {
            return $value;
        }

        if (false === ($userInfo = posix_getpwnam($value))) {
            return null;
        }

        return $userInfo['uid'];
    }

    /**
     * @param string $value
     *
     * @return int|null
     */
    private function parseGroupValue($value)
    {
        if (is_numeric($value) && false !== posix_getgrgid($value)) {
            return $value;
        }

        if (false === ($groupInfo = posix_getgrnam($value))) {
            return null;
        }

        return $groupInfo['gid'];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function parseModeValue($value)
    {
        return $value;
    }

    public function parse($permissions)
    {
        $uid = null;
        $gid = null;
        $mode = null;
        //
        $recursive = false;

        foreach (explode(';', $permissions) as $part) {
            if (0 === preg_match(self::EXPR, $part, $matched)) {
                continue;
            }

            $value = $matched[2];

            switch ($matched[1]) {
                case 'user':
                    $uid = $this->parseUserValue($value);
                    break;
                case 'group':
                    $gid = $this->parseGroupValue($value);
                    break;
                case 'mode':
                    $mode = $this->parseModeValue($value);
                    break;
                case 'recursive':
                    $recursive = $this->parseRecursiveValue($value);
                    break;
            }
        }

        return [$uid, $gid, $mode, $recursive];
    }
}

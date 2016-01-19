<?php


namespace CodeTool\ArtifactDownloader\Fs\Util;


class FsUtilChmodArgParser implements FsUtilChmodArgParserInterface
{
    const MODE_EXPR = '/^(?P<users>[ugoa]+)(?P<operator>[-+=])(?P<perms>[rwxXst]*|[ugo])$/';

    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $user
     * @param array        $perms
     *
     * @return int
     */
    private function getOctalModeFromSymbolicPerms(\SplFileInfo $fileInfo, $user, array $perms)
    {
        $prevMode = $fileInfo->getPerms() & self::PERM_BITS;
        $hasXPermission = ($prevMode & self::EXEC_PERM_BITS) > 0;
        $applyXPermission = $fileInfo->isDir() || $hasXPermission;

        $XPerms = [
            'u' => ['X' => 0],
            'g' => ['X' => 0],
            'o' => ['X' => 0],
        ];
        if (true === $applyXPermission) {
            $XPerms = [
                'u' => ['X' => self::S_IXUSR],
                'g' => ['X' => self::S_IXGRP],
                'o' => ['X' => self::S_IXOTH],
            ];
        }

        $userPermsToModes = [
            'u' => [
                'r' => self::S_IRUSR,
                'w' => self::S_IWUSR,
                'x' => self::S_IXUSR,
                's' => self::S_ISUID,
                't' => 0,
                'u' => $prevMode & self::S_IRWXU,
                'g' => ($prevMode & self::S_IRWXG) << 3,
                'o' => ($prevMode & self::S_IRWXO) << 6
            ],
            'g' => [
                'r' => self::S_IRGRP,
                'w' => self::S_IWGRP,
                'x' => self::S_IXGRP,
                's' => self::S_ISGID,
                't' => 0,
                'u' => ($prevMode & self::S_IRWXU) >> 3,
                'g' => $prevMode & self::S_IRWXG,
                'o' => ($prevMode & self::S_IRWXO) << 3
            ],
            'o' => [
                'r' => self::S_IROTH,
                'w' => self::S_IWOTH,
                'x' => self::S_IXOTH,
                's' => 0,
                't' => self::S_ISVTX,
                'u' => ($prevMode & self::S_IRWXU) >> 6,
                'g' => ($prevMode & self::S_IRWXG) >> 3,
                'o' => $prevMode & self::S_IRWXO
            ]
        ];

        foreach ($XPerms as $key => $value) {
            $userPermsToModes[$key] = array_replace($userPermsToModes[$key], $value);
        }

        return array_reduce($perms, function ($mode, $perm) use ($userPermsToModes, $user) {
            return $mode | $userPermsToModes[$user][$perm];
        }, 0);
    }

    /**
     * @param string $user
     * @param string $operator
     * @param int    $modeToApply
     * @param int    $currentMode
     *
     * @return int
     */
    private function applyOperationToMode($user, $operator, $modeToApply, $currentMode)
    {
        if ('+' === $operator) {
            return $currentMode | $modeToApply;
        }

        if ('-' === $operator) {
            return $currentMode - ($currentMode & $modeToApply);
        }

        // '=' === $operator
        switch ($user) {
            case 'u':
                $mask = self::S_IRWXU | self::S_ISUID;
                break;
            case 'g':
                $mask = self::S_IRWXG | self::S_ISUID;
                break;
            case 'o':
                $mask = self::S_IRWXO | self::S_ISVTX;
                break;
        }

        $inverseMask = $mask ^ self::PERM_BITS;

        return ($currentMode & $inverseMask) | $modeToApply;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $symbolicMode
     *
     * @return int
     */
    private function symbolicModeToOctal(\SplFileInfo $fileInfo, $symbolicMode)
    {
        $newMode = $fileInfo->getPerms() & self::PERM_BITS;

        foreach (explode(',', $symbolicMode) as $mode) {
            if (0 === preg_match(self::MODE_EXPR, $mode, $matches)) {
                continue;
            }

            $users = $matches['users'];
            $operator = $matches['operator'];

            $perms = [];
            if ('' !== $matches['perms']) {
                $perms = str_split($matches['perms']);
            }

            if ('a' === $users) {
                $users = 'ugo';
            }


            foreach (str_split($users) as $user) {
                $modeToApply = $this->getOctalModeFromSymbolicPerms($fileInfo, $user, $perms);
                $newMode = $this->applyOperationToMode($user, $operator, $modeToApply, $newMode);
            }
        }

        return $newMode;
    }

    /**
     * @param string $x
     *
     * @return bool
     */
    private function isOctal($x)
    {
        return decoct(octdec($x)) === (string) $x;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $mode
     *
     * @return int
     */
    public function parseMode(\SplFileInfo $fileInfo, $mode)
    {
        if ($this->isOctal($mode)) {
            return octdec($mode);
        }

        return $this->symbolicModeToOctal($fileInfo, $mode);
    }
}

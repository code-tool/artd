<?php

namespace CodeTool\ArtifactDownloader\Scope\Config;

class ScopeConfigChildNode implements ScopeConfigChildNodeInterface
{
    /**
     * @var string
     */
    private $relativePath;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $sourceHash;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $permissions;

    /**
     * @param string $relativePath
     * @param string $type
     * @param string $sourcePath
     * @param string $sourceHash
     * @param string $target
     * @param string $permissions
     */
    public function __construct($relativePath, $type, $sourcePath, $sourceHash, $target, $permissions)
    {
        $this->relativePath = $relativePath;
        $this->type = $type;
        $this->sourcePath = $sourcePath;
        $this->sourceHash = $sourceHash;
        $this->target = $target;
        $this->permissions = $permissions;
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * @return string
     */
    public function getSourceHash()
    {
        return $this->sourceHash;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}

<?php

namespace CodeTool\ArtifactDownloader\DomainObject;

interface DomainObjectInterface extends \Countable, \Iterator
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOrDefault($key, $default = null);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return DomainObjectInterface
     */
    public function set($key, $value);
}

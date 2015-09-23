<?php

namespace CodeTool\ArtifactDownloader\DomainObject;

class DomainObject implements \Countable, DomainObjectInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (false === $this->has($key)) {
            throw new \InvalidArgumentException('');
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOrDefault($key, $default = null)
    {
        if (false === $this->has($key)) {
            return $default;
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
}

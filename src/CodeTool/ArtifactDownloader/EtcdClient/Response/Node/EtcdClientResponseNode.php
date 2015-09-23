<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Response\Node;

class EtcdClientResponseNode implements EtcdClientResponseNodeInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $modifiedIndex;

    /**
     * @var string
     */
    private $createdIndex;

    /**
     * @var string|null
     */
    private $expiration;

    /**
     * @var int|null
     */
    private $ttl;

    /**
     * @param string $key
     * @param string $value
     * @param string $modifiedIndex
     * @param string $createdIndex
     * @param string $expiration
     * @param int    $ttl
     */
    public function __construct($key, $value, $modifiedIndex, $createdIndex, $expiration = null, $ttl = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->modifiedIndex = $modifiedIndex;
        $this->createdIndex = $createdIndex;

        $this->expiration = $expiration;
        $this->ttl = $ttl;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getModifiedIndex()
    {
        return $this->modifiedIndex;
    }

    /**
     * @return string
     */
    public function getCreatedIndex()
    {
        return $this->createdIndex;
    }

    /**
     * @return int|null
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @return null|string
     */
    public function getExpiration()
    {
        return $this->expiration;
    }
}

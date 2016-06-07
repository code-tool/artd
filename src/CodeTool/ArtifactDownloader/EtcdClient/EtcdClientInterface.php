<?php
namespace CodeTool\ArtifactDownloader\EtcdClient;

interface EtcdClientInterface
{
    const ERROR_CODE_EVENT_INDEX_CLEARED = 401;

    /**
     * Set the value of a key
     *
     * @param string   $key
     * @param string   $value
     * @param int|null $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function set($key, $value, $ttl = null);

    /**
     * Retrieve the value of a key
     *
     * @param string $key
     *
     * @return Result\EtcdClientResultInterface
     */
    public function get($key);

    /**
     * Update an existing key with a given value.
     *
     * @param string $key
     * @param string $value
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function update($key, $value, $ttl = 0);

    /**
     * remove a key
     *
     * @param string $key
     *
     * @return Result\EtcdClientResultInterface
     */
    public function rm($key);

    /**
     * make a new key with a given value
     *
     * @param string $key
     * @param string $value
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function mk($key, $value, $ttl = 0);

    /**
     * make a new directory
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function mkdir($key, $ttl = 0);

    /**
     * Update directory
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function updateDir($key, $ttl);

    /**
     * Removes the key if it is directory
     *
     * @param string $key
     * @param bool   $recursive
     *
     * @return Result\EtcdClientResultInterface
     */
    public function rmdir($key, $recursive = false);

    /**
     * @param string $key
     * @param int    $waitIndex
     *
     * @return Result\EtcdClientResultInterface
     */
    public function watch($key, $waitIndex);
}

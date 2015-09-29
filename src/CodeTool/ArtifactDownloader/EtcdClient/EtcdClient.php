<?php

namespace CodeTool\ArtifactDownloader\EtcdClient;

use CodeTool\ArtifactDownloader\EtcdClient\Result\Factory\EtcdClientResultFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;

class EtcdClient implements EtcdClientInterface
{
    const DEFAULT_SERVER = 'http://127.0.0.1:4001';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var EtcdClientResultFactoryInterface
     */
    private $etcdClientResultFactory;

    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $root = '/';

    /**
     * @param HttpClientInterface              $httpClient
     * @param EtcdClientResultFactoryInterface $etcdClientResultFactory
     * @param string                           $root
     * @param string                           $server
     * @param string                           $apiVersion
     */
    public function __construct(
        HttpClientInterface $httpClient,
        EtcdClientResultFactoryInterface $etcdClientResultFactory,
        $root = '/',
        $server = self::DEFAULT_SERVER,
        $apiVersion = 'v2'
    ) {
        $this->httpClient = $httpClient;
        $this->etcdClientResultFactory = $etcdClientResultFactory;

        $this->root = $root;
        $this->server = rtrim($server, '/');
        $this->apiVersion = $apiVersion;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $parameters
     *
     * @return Result\EtcdClientResultInterface
     */
    private function doRequest($uri, $method, array $parameters = [])
    {
        $httpResponse = $this->httpClient->makeRequest($uri, $method, $parameters);

        return $this->etcdClientResultFactory->createFromJson($httpResponse->getResponse()->getBody());
    }

    /**
     * Build key space operations
     *
     * @param string   $key
     * @param string[] $params
     *
     * @return string
     */
    private function buildKeyUri($key, array $params = [])
    {
        $result = sprintf('%s/%s/keys%s%s', $this->server, $this->apiVersion, $this->root, $key);

        if ([] !== $params) {
            $result .= '?' . http_build_query($params);
        }

        return $result;
    }

    /**
     * Set the value of a key
     *
     * @param string   $key
     * @param string   $value
     * @param int|null $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function set($key, $value, $ttl = null)
    {
        $data = ['value' => $value];

        if (null !== $ttl) {
            $data['ttl'] = $ttl;
        }

        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_PUT, $data);
    }

    /**
     * @param string $key
     *
     * @return Result\EtcdClientResultInterface
     */
    public function get($key)
    {
        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_GET);
    }

    /**
     * Update an existing key with a given value.
     *
     * @param string   $key
     * @param string   $value
     * @param int|null $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function update($key, $value, $ttl = 0)
    {
        $data = ['value' => $value, 'prevExist' => 'true'];

        if (null !== $ttl) {
            $data['ttl'] = $ttl;
        }

        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_PUT, $data);
    }

    /**
     * remove a key
     *
     * @param string $key
     *
     * @return Result\EtcdClientResultInterface
     */
    public function rm($key)
    {
        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_DELETE);
    }

    /**
     * make a new key with a given value
     *
     * @param string $key
     * @param string $value
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function mk($key, $value, $ttl = 0)
    {
        $data = ['value' => $value, 'prevExist' => 'true'];

        if (null !== $ttl) {
            $data['ttl'] = $ttl;
        }

        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_PUT, $data);
    }

    /**
     * make a new directory
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function mkdir($key, $ttl = 0)
    {
        $data = ['dir' => 'true', 'prevExist' => 'false'];

        if (0 !== $ttl) {
            $data['ttl'] = $ttl;
        }

        return $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_PUT, $data);
    }

    /**
     * Update directory
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return Result\EtcdClientResultInterface
     */
    public function updateDir($key, $ttl)
    {
        $data = ['dir' => 'true', 'prevExist' => 'true', 'ttl' => (int)$ttl];

        $this->doRequest($this->buildKeyUri($key), HttpClientInterface::METHOD_PUT, $data);
    }

    /**
     * Removes the key if it is directory
     *
     * @param string $key
     * @param bool   $recursive
     *
     * @return Result\EtcdClientResultInterface
     */
    public function rmdir($key, $recursive = false)
    {
        $query = ['dir' => 'true'];

        if (true === $recursive) {
            $query['recursive'] = 'true';
        }

        return $this->doRequest($this->buildKeyUri($key, $query), HttpClientInterface::METHOD_DELETE);
    }

    /**
     * @param string $key
     * @param int    $waitIndex
     *
     * @return Result\EtcdClientResultInterface
     */
    public function watch($key, $waitIndex)
    {
        $query = [
            'wait' => 'true',
            'waitIndex' => $waitIndex
        ];

        return $this->doRequest($this->buildKeyUri($key, $query), HttpClientInterface::METHOD_GET);
    }
}

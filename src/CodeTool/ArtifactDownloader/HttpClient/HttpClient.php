<?php

namespace CodeTool\ArtifactDownloader\HttpClient;

use CodeTool\ArtifactDownloader\HttpClient\Response\Factory\HttpClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\Factory\HttpClientResultFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class HttpClient implements HttpClientInterface
{
    private $ch;

    /**
     * @var HttpClientResultFactoryInterface
     */
    private $httpClientResultFactory;

    /**
     * @var HttpClientResponseFactoryInterface
     */
    private $httpClientResponseFactory;

    /**
     * @var ResourceCredentialsRepositoryInterface
     */
    private $resourceCredentialsRepository;

    /**
     * @var string[]
     */
    private $responseHeaders = [];

    public function __construct(
        HttpClientResultFactoryInterface $httpClientResultFactory,
        HttpClientResponseFactoryInterface $httpClientResponseFactory,
        ResourceCredentialsRepositoryInterface $resourceCredentialsRepository
    ) {
        $this->httpClientResultFactory = $httpClientResultFactory;
        $this->httpClientResponseFactory = $httpClientResponseFactory;
        $this->resourceCredentialsRepository = $resourceCredentialsRepository;
    }

    private function getCurlHandle()
    {
        if (null === $this->ch) {
            return $this->ch = curl_init();
        }

        curl_reset($this->ch);
        return $this->ch;
    }

    /**
     * @param resource $ch
     * @param string   $url
     */
    private function addResourceCredentials($ch, $url)
    {
        $resourceCredentials = $this->resourceCredentialsRepository->getCredentialsByResourcePath($url);
        if (null === $resourceCredentials) {
            return;
        }

        if (null !== $resourceCredentials->getClientCertPath()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPath());

            if (null !== $resourceCredentials->getClientCertPassword()) {
                curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $resourceCredentials->getClientCertPassword());
            }
        }

        if (null !== $resourceCredentials->getHttpProxy()) {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $resourceCredentials->getHttpProxy());
        }
    }

    /**
     * @param resource $ch
     */
    private function setBasicCurlParameters($ch)
    {
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // enable keep-alive
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 5);
        curl_setopt($ch, CURLOPT_TCP_KEEPINTVL, 5);
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    }

    private function createResultFromChAndResponse($ch)
    {
        if (false === ($response = curl_exec($ch))) {
            return $this->httpClientResultFactory->createError(curl_error($ch), $this);
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = $this->httpClientResponseFactory->make($httpCode, $this->responseHeaders, $response);

        if ($httpCode < 200 || $httpCode > 399) {
            return $this->httpClientResultFactory->createErrorWithResponse(
                $response,
                sprintf('HTTP error: code %d', $httpCode),
                $this
            );
        }

        return $this->httpClientResultFactory->createSuccessful($response);
    }

    /**
     * @param string $url
     *
     * @return resource
     */
    private function getNewCurlHandle($url)
    {
        $ch = $this->getCurlHandle();

        curl_setopt($ch, CURLOPT_URL, $url);
        $this->setBasicCurlParameters($ch);

        $this->responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) {
            $this->responseHeaders[] = $header;

            return strlen($header);
        });

        return $ch;
    }

    /**
     * @param string       $uri
     * @param string       $method
     * @param string|array $body
     *
     * @return HttpClientResultInterface
     */
    public function makeRequest($uri, $method, $body)
    {
        $ch = $this->getNewCurlHandle($uri);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (false === empty($body)) {
            if (true === is_array($body)) {
                $body = http_build_query($body, '', '&');
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        return $this->createResultFromChAndResponse($ch);
    }

    /**
     * @param string $url
     * @param string $target
     *
     * @return HttpClientResultInterface
     */
    public function downloadFile($url, $target)
    {
        $ch = $this->getNewCurlHandle($url);

        if (false === ($targetFileHandle = @fopen($target, 'w+'))) {
            return $this->httpClientResultFactory->createError(sprintf('Can\'t open file "%s" for writing.', $target));
        }

        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);
        $this->addResourceCredentials($ch, $url);

        $result = $this->createResultFromChAndResponse($ch);
        fclose($targetFileHandle);

        return $result;
    }

    public function __destruct()
    {
        if (null !== $this->ch) {
            curl_close($this->ch);
            $this->ch = null;
        }
    }
}

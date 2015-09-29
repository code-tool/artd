<?php

namespace CodeTool\ArtifactDownloader\HttpClient;

use CodeTool\ArtifactDownloader\HttpClient\Response\Factory\HttpClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\Factory\HttpClientResultFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\Result\HttpClientResultInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class HttpClient implements HttpClientInterface
{
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

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPath());
        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPassword());
    }

    /**
     * @param resource $ch
     */
    private function setBasicCurlParameters($ch)
    {
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    }

    /**
     * @param string $url
     *
     * @return resource
     */
    private function getNewCurlHandle($url)
    {
        $ch = curl_init($url);
        $this->setBasicCurlParameters($ch);

        $this->responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$responseHeaders) {
            $this->responseHeaders[] = $header;

            return strlen($header);
        });

        return $ch;
    }

    private function createResultFromChAndResponse($ch)
    {
        if (false === ($response = curl_exec($ch))) {
            return $this->httpClientResultFactory->createError(curl_error($ch));
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode > 399) {
            return $this->httpClientResultFactory->createError(sprintf('HTTP error: code %d', $httpCode));
        }

        return $this->httpClientResultFactory->createSuccessful(
            $httpCode,
            $this->responseHeaders,
            $response
        );
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
            curl_close($ch);

            return $this->httpClientResultFactory->createError(sprintf('Can\'t open file "%s" for writing.', $target));
        }

        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);
        $this->addResourceCredentials($ch, $url);

        $result = $this->createResultFromChAndResponse($ch);
        fclose($targetFileHandle);

        return $result;
    }
}

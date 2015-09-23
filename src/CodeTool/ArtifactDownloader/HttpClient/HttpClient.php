<?php

namespace CodeTool\ArtifactDownloader\HttpClient;

use CodeTool\ArtifactDownloader\HttpClient\Response\Factory\HttpClientResponseFactoryInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @var HttpClientResponseFactoryInterface
     */
    private $httpClientResponseFactory;

    /**
     * @var ResourceCredentialsRepositoryInterface
     */
    private $resourceCredentialsRepository;

    public function __construct(
        HttpClientResponseFactoryInterface $httpClientResponseFactory,
        ResourceCredentialsRepositoryInterface $resourceCredentialsRepository
    ) {
        $this->httpClientResponseFactory = $httpClientResponseFactory;
        $this->resourceCredentialsRepository = $resourceCredentialsRepository;
    }

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

    private function setBasicCurlParameters($ch)
    {
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    }

    private function getNewCurlHandle($url)
    {
        $ch = curl_init($url);
        $this->setBasicCurlParameters($ch);

        return $ch;
    }

    /**
     * @param string       $uri
     * @param string       $method
     * @param string|array $body
     *
     * @return Response\HttpClientResponse
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
        //
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$responseHeaders) {
            $responseHeaders[] = $header;

            return strlen($header);
        });

        if (false === ($response = curl_exec($ch))) {
            throw new \RuntimeException(curl_error($ch));
        }

        $requestInfo = curl_getinfo($ch);
        curl_close($ch);

        return $this->httpClientResponseFactory->make(
            (int)$requestInfo['http_code'],
            $responseHeaders,
            $response
        );
    }

    /**
     * @param string $url
     * @param string $target
     *
     * @return null|string
     */
    public function downloadFile($url, $target)
    {
        $ch = $this->getNewCurlHandle($url);

        if (false === ($targetFileHandle = @fopen($target, 'w+'))) {
            curl_close($ch);

            return sprintf('Can\'t open file "%s" for writing.', $target);
        }

        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);
        $this->addResourceCredentials($ch, $url);

        if (false === curl_exec($ch)) {
            curl_close($ch);

            return sprintf('Can\'t download file %s. %s', $url, curl_error($ch));
        }
        fclose($targetFileHandle);

        return null;
    }
}

<?php

namespace CodeTool\ArtifactDownloader\Command;


use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandDownloadFile implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $target;

    /**
     * @param ResultFactoryInterface $commandResultFactory
     * @param HttpClientInterface    $httpClient
     * @param string                 $url
     * @param string                 $target
     */
    public function __construct(
        ResultFactoryInterface $commandResultFactory,
        HttpClientInterface $httpClient,
        $url,
        $target
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->httpClient = $httpClient;
        $this->url = $url;
        $this->target = $target;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->httpClient->downloadFile($this->url, $this->target);
    }

    public function __toString()
    {
        return sprintf('%s: download %s -> %s', __CLASS__, $this->url, $this->target);
    }
}

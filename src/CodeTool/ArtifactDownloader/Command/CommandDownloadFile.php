<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;
use CodeTool\ArtifactDownloader\HttpClient\HttpClientInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class CommandDownloadFile implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
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
     * @param CommandResultFactoryInterface $commandResultFactory
     * @param HttpClientInterface           $httpClient
     * @param string                        $url
     * @param string                        $target
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
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
     * @return CommandResultInterface
     */
    public function execute()
    {
        if (null !== ($error = $this->httpClient->downloadFile($this->url, $this->target))) {
            return $this->commandResultFactory->createError($error);
        }

        return $this->commandResultFactory->createSuccess();
    }
}

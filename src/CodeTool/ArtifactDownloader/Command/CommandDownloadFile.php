<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;
use CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactoryInterface;
use CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepositoryInterface;

class CommandDownloadFile implements CommandInterface
{
    /**
     * @var CommandResultFactoryInterface
     */
    private $commandResultFactory;

    /**
     * @var ResourceCredentialsRepositoryInterface
     */
    private $resourceCredentialsRepository;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $target;

    /**
     * @param CommandResultFactoryInterface          $commandResultFactory
     * @param ResourceCredentialsRepositoryInterface $resourceCredentialsRepository
     * @param string                                 $url
     * @param string                                 $target
     */
    public function __construct(
        CommandResultFactoryInterface $commandResultFactory,
        ResourceCredentialsRepositoryInterface $resourceCredentialsRepository,
        $url,
        $target
    ) {
        $this->commandResultFactory = $commandResultFactory;
        $this->resourceCredentialsRepository = $resourceCredentialsRepository;
        $this->url = $url;
        $this->target = $target;
    }

    private function addResourceCredentials($ch)
    {
        $resourceCredentials = $this->resourceCredentialsRepository->getCredentialsByResourcePath($this->url);
        if (null === $resourceCredentials) {
            return;
        }

        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPath());
        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPassword());
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        $ch = curl_init($this->url);

        $targetFileHandle = fopen($this->target, 'w+');
        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);

        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $this->addResourceCredentials($ch);

        curl_exec($ch); // get curl response
        curl_close($ch);

        fclose($targetFileHandle);

        return $this->commandResultFactory->createSuccess();
    }
}

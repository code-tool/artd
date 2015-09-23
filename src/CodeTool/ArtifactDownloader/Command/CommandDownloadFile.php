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

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPath());
        curl_setopt($ch, CURLOPT_SSLCERT, $resourceCredentials->getClientCertPassword());
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        $ch = curl_init($this->url);

        if (false === ($targetFileHandle = @fopen($this->target, 'w+'))) {
            curl_close($ch);

            return $this->commandResultFactory->createErrorFromGetLast(
                sprintf('Can\'t open file "%s" for writing.', $this->target)
            );
        }
        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);

        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $this->addResourceCredentials($ch);

        if (false === curl_exec($ch)) {
            curl_close($ch);

            return $this->commandResultFactory->createError(
                sprintf('Can\'t download file %s. %s', $this->url, curl_error($ch))
            );
        }

        fclose($targetFileHandle); // non-fatal error. ignore

        return $this->commandResultFactory->createSuccess();
    }
}

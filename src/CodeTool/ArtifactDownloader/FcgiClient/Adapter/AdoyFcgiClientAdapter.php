<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Adapter;

use Adoy\FastCGI\Client;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactoryInterface;
use CodeTool\ArtifactDownloader\FcgiClient\FcgiClientInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Result\Factory\FcgiClientResultFactoryInterface;

class AdoyFcgiClientAdapter implements FcgiClientInterface
{
    /**
     * @var FcgiClientResultFactoryInterface
     */
    private $fcgiClientResultFactory;

    /**
     * @var ErrorFactoryInterface
     */
    private $errorFactory;

    public function __construct(
        FcgiClientResultFactoryInterface $fcgiClientResultFactory,
        ErrorFactoryInterface $errorFactory
    ) {
        $this->fcgiClientResultFactory = $fcgiClientResultFactory;
        $this->errorFactory = $errorFactory;
    }

    /**
     * @param string $socketPath
     * @param array  $headers
     * @param string $stdin
     *
     * @return \CodeTool\ArtifactDownloader\FcgiClient\Result\FcgiClientResultInterface
     */
    public function makeRequest($socketPath, array $headers, $stdin)
    {
        $adoyClient = new Client($socketPath, null);

        $error = null;
        $response = null;

        try {
            $response = $adoyClient->request($headers, $stdin);
        } catch (\Exception $e) {
            $error = $this->errorFactory->createFromException($e);
        }

        return $this->fcgiClientResultFactory->make($response, $error);
    }
}

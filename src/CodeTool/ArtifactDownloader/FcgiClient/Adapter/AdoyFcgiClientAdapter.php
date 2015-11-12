<?php

namespace CodeTool\ArtifactDownloader\FcgiClient\Adapter;

use Adoy\FastCGI\Client;
use CodeTool\ArtifactDownloader\FcgiClient\FcgiClientInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Result\Factory\FcgiClientResultFactoryInterface;

class AdoyFcgiClientAdapter implements FcgiClientInterface
{
    /**
     * @var FcgiClientResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @param FcgiClientResultFactoryInterface $resultFactory
     */
    public function __construct(FcgiClientResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
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

        try {
            $result = $this->resultFactory->createSuccess($adoyClient->request($headers, $stdin));
        } catch (\Exception $e) {
            $result = $this->resultFactory->createErrorFromException($e);
        }

        return $result;
    }
}

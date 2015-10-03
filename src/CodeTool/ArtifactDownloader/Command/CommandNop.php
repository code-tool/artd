<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;

class CommandNop implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @param ResultFactoryInterface $resultFactory
     */
    public function __construct(ResultFactoryInterface $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->createSuccessful();
    }
}

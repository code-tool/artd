<?php

namespace CodeTool\ArtifactDownloader\Runit\Command;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Runit\RunitInterface;

class RunitCommandSetServiceState implements CommandInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var RunitInterface
     */
    private $runit;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $targetState;

    /**
     * @var bool
     */
    private $fatal;

    /**
     * CommandRunitServiceStatus constructor.
     *
     * @param ResultFactoryInterface $resultFactory
     * @param RunitInterface         $runit
     * @param string                 $serviceName
     * @param string                 $targetState
     * @param bool                   $fatal
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        RunitInterface $runit,
        $serviceName,
        $targetState,
        $fatal = true
    ) {
        $this->resultFactory = $resultFactory;
        $this->runit = $runit;
        //
        $this->serviceName = $serviceName;
        $this->targetState = $targetState;
        $this->fatal = $fatal;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->runit->setServiceState($this->serviceName, $this->targetState);
        if (false === $this->fatal && false === $result->isSuccessful()) {
            return $this->resultFactory->createSuccessful();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'runit service=%s, state=%s, fatal=%s',
            $this->serviceName,
            $this->targetState,
            $this->fatal ? 't' : 'f'
        );
    }
}

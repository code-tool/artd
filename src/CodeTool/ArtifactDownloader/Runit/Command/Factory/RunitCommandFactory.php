<?php

namespace CodeTool\ArtifactDownloader\Runit\Command\Factory;

use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Runit\Command\RunitCommandSetServiceState;
use CodeTool\ArtifactDownloader\Runit\RunitInterface;

class RunitCommandFactory implements RunitCommandFactoryInterface
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
     * @param ResultFactoryInterface $resultFactory
     * @param RunitInterface         $runit
     */
    public function __construct(ResultFactoryInterface $resultFactory, RunitInterface $runit)
    {
        $this->resultFactory = $resultFactory;
        $this->runit = $runit;
    }

    /**
     * @param string    $serviceName
     * @param string    $targetState
     * @param bool      $fatal
     *
     * @return RunitCommandSetServiceState
     */
    public function createSetServiceStateCommand($serviceName, $targetState, $fatal = true)
    {
        return new RunitCommandSetServiceState($this->resultFactory, $this->runit, $serviceName, $targetState, $fatal);
    }
}

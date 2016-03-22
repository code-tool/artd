<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\FcgiClient\Command\Factory\FcgiCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeConfigProcessorRuleFpmOpcahceFlushHandler implements ScopeConfigProcessorRuleHandlerInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var FsCommandFactoryInterface
     */
    private $fsCommandFactory;

    /**
     * @var FcgiCommandFactoryInterface
     */
    private $fcgiCommandFactory;

    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var string
     */
    private $resourceDir;

    /**
     * ScopeConfigProcessorRuleFpmOpcahceFlushHandler constructor.
     *
     * @param ResultFactoryInterface      $resultFactory
     * @param FsCommandFactoryInterface   $fsCommandFactory
     * @param FcgiCommandFactoryInterface $fcgiCommandFactory
     * @param BasicUtil                   $basicUtil
     * @param                             $resourceDir
     */
    public function __construct(
        ResultFactoryInterface $resultFactory,
        FsCommandFactoryInterface $fsCommandFactory,
        FcgiCommandFactoryInterface $fcgiCommandFactory,
        BasicUtil $basicUtil,
        $resourceDir
    ) {
        $this->resultFactory = $resultFactory;
        $this->fsCommandFactory = $fsCommandFactory;
        $this->fcgiCommandFactory = $fcgiCommandFactory;
        $this->basicUtil = $basicUtil;
        $this->resourceDir = $resourceDir;
    }

    /**
     * @return string[]
     */
    public function getSupportedTypes()
    {
        return ['fpm-opcache-flush'];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return ResultInterface
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $targetFilePath =
            $scopeInfo->getPath() .
            DIRECTORY_SEPARATOR .
            $this->basicUtil->getTmpName('artd-fpm-opcache-flush-', '.php');

        $collection
            ->add(
                $this->fsCommandFactory->createWriteFileCommand(
                    $targetFilePath,
                    file_get_contents($this->resourceDir . '/opcache_flush.php')
                )
            )->add(
                $this->fcgiCommandFactory->createFcgiRequestCommand(
                    $scopeConfigRule->get('socket'),
                    [
                        'GATEWAY_INTERFACE' => 'FastCGI/1.0',
                        'REQUEST_METHOD' => 'POST',
                        'SCRIPT_NAME' => basename($targetFilePath),
                        'SCRIPT_FILENAME' => $targetFilePath,
                        'REMOTE_ADDR' => '127.0.0.1',
                        'SERVER_ADDR' => '127.0.0.1',
                        'SERVER_PROTOCOL' => 'HTTP/1.0',
                        'CONTENT_TYPE' => 'text/html',
                        'CONTENT_LENGTH' => 0,
                    ],
                    ''
                )
            )->add(
                $this->fsCommandFactory->createRmCommand($targetFilePath)
            );

        return $this->resultFactory->createSuccessful();
    }
}

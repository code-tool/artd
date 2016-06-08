<?php

namespace CodeTool\ArtifactDownloader\Config\Provider;

use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactory;
use CodeTool\ArtifactDownloader\Config\Provider\Result\Factory\ConfigProviderResultFactory;
use CodeTool\ArtifactDownloader\DomainObject\Factory\DomainObjectFactory;
use CodeTool\ArtifactDownloader\Error\Factory\ErrorFactory;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetails;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientError;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClient;
use CodeTool\ArtifactDownloader\EtcdClient\EtcdClientInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Response\EtcdClientSingleNodeResponse;
use CodeTool\ArtifactDownloader\EtcdClient\Response\Node\EtcdClientResponseNode;
use CodeTool\ArtifactDownloader\EtcdClient\Result\EtcdClientResult;
use CodeTool\ArtifactDownloader\Scope\Config\Factory\ScopeConfigFactory;

class ConfigProviderEtcdTest extends \PHPUnit_Framework_TestCase
{
    private function buildEtcdClientMockBuilder()
    {
        return $this->getMockBuilder(EtcdClient::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->setMethods(['get', 'watch']);
    }

    private function makeNewInstance(EtcdClientInterface $etcdClient)
    {
        $errorFactory = new ErrorFactory();
        $configFactory = new ConfigFactory(new DomainObjectFactory(), new ScopeConfigFactory());
        $configProviderResultFactory = new ConfigProviderResultFactory();

        return new ConfigProviderEtcd(
            $errorFactory,
            $configFactory,
            $configProviderResultFactory,
            $etcdClient,
            'dummy_path'
        );
    }

    public function testGetConfigWithoutRevision()
    {
        $etcdClientMockBuilder = $this->buildEtcdClientMockBuilder();
        $defaultConfigStr = file_get_contents(__DIR__ . '/../../../../../resource/sample-config.json');
        $getResult = new EtcdClientResult(
            null,
            new EtcdClientSingleNodeResponse(0, 'get', new EtcdClientResponseNode('', $defaultConfigStr, 0, 0))
        );

        $mock = $etcdClientMockBuilder->getMock();
        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('dummy_path'))
            ->willReturn($getResult);

        $this->makeNewInstance($mock)->getConfigAfterRevision(null);
    }

    public function testGetConfigAfterRevision()
    {
        $etcdClientMockBuilder = $this->buildEtcdClientMockBuilder();
        $defaultConfigStr = file_get_contents(__DIR__ . '/../../../../../resource/sample-config.json');
        $watchResult = new EtcdClientResult(
            null,
            new EtcdClientSingleNodeResponse(0, 'watch', new EtcdClientResponseNode('', $defaultConfigStr, 0, 0))
        );

        $mock = $etcdClientMockBuilder->getMock();
        $mock->expects($this->once())
            ->method('watch')
            ->with($this->equalTo('dummy_path'), $this->equalTo(2))
            ->willReturn($watchResult);

        $this->makeNewInstance($mock)->getConfigAfterRevision(1);
    }

    public function testWatchAfterExpiredIndex()
    {
        $etcdClientMockBuilder = $this->buildEtcdClientMockBuilder();
        $defaultConfigStr = file_get_contents(__DIR__ . '/../../../../../resource/sample-config.json');

        $watchResult = new EtcdClientResult(
            new EtcdClientError(
                'Index expired',
                new EtcdClientErrorDetails('ssss', EtcdClientInterface::ERROR_CODE_EVENT_INDEX_CLEARED, 1, '')
            ),
            null
        );

        $getResult = new EtcdClientResult(
            null,
            new EtcdClientSingleNodeResponse(0, 'get', new EtcdClientResponseNode('', $defaultConfigStr, 2, 0))
        );

        $etcdClientMock = $etcdClientMockBuilder->getMock();
        $etcdClientMock->expects($this->at(0))
            ->method('watch')
            ->with($this->equalTo('dummy_path'), $this->equalTo(2))
            ->willReturn($watchResult);

        $etcdClientMock->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('dummy_path'))
            ->willReturn($getResult);

        $this->makeNewInstance($etcdClientMock)->getConfigAfterRevision(1);
    }

    public function testWatchAfterExpiredIndexWithoutNewModifiedIndex()
    {
        $etcdClientMockBuilder = $this->buildEtcdClientMockBuilder();
        $defaultConfigStr = file_get_contents(__DIR__ . '/../../../../../resource/sample-config.json');

        $watchResult1 = new EtcdClientResult(
            new EtcdClientError(
                'Index expired',
                new EtcdClientErrorDetails('ssss', EtcdClientInterface::ERROR_CODE_EVENT_INDEX_CLEARED, 1000, '')
            ),
            null
        );

        $getResult = new EtcdClientResult(
            null,
            new EtcdClientSingleNodeResponse(0, 'get', new EtcdClientResponseNode('', $defaultConfigStr, 1, 0))
        );

        $watchResult2 = new EtcdClientResult(
            null,
            new EtcdClientSingleNodeResponse(0, 'watch', new EtcdClientResponseNode('', $defaultConfigStr, 1001, 0))
        );

        $etcdClientMock = $etcdClientMockBuilder->getMock();
        $etcdClientMock->expects($this->at(0))
            ->method('watch')
            ->with($this->equalTo('dummy_path'), $this->equalTo(2))
            ->willReturn($watchResult1);

        $etcdClientMock->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('dummy_path'))
            ->willReturn($getResult);

        $etcdClientMock->expects($this->at(2))
            ->method('watch')
            ->with($this->equalTo('dummy_path'), $this->equalTo(1001))
            ->willReturn($watchResult2);

        $this->makeNewInstance($etcdClientMock)->getConfigAfterRevision(1);
    }
}

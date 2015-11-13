<?php

namespace {

    use CodeTool\ArtifactDownloader\Archive;
    use CodeTool\ArtifactDownloader\ArtifactDownloader;
    use CodeTool\ArtifactDownloader\Command;
    use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactory;
    use CodeTool\ArtifactDownloader\CmdRunner;
    use CodeTool\ArtifactDownloader\DomainObject;
    use CodeTool\ArtifactDownloader\DirectoryComparator;
    use CodeTool\ArtifactDownloader\Error;
    use CodeTool\ArtifactDownloader\EtcdClient;
    use CodeTool\ArtifactDownloader\HttpClient;
    use CodeTool\ArtifactDownloader\FcgiClient;
    use CodeTool\ArtifactDownloader\Fs;
    use CodeTool\ArtifactDownloader\ResourceCredentials;
    use CodeTool\ArtifactDownloader\Result;
    use CodeTool\ArtifactDownloader\Runit;
    use CodeTool\ArtifactDownloader\UnitConfig;
    use CodeTool\ArtifactDownloader\UnitStatusBuilder;
    use CodeTool\ArtifactDownloader\Util;
    use CodeTool\ArtifactDownloader\Scope;
    use fool\echolog\Echolog;
    use Pimple\Container;
    use Psr\Log;

    $container = new Container();

    //
    $container['logger'] = function () {
        return new Echolog(); // Log\NullLogger();
    };

    //
    $container['unit_config'] = function () {
        return new UnitConfig\UnitConfig();
    };

    //
    $container['error.factory'] = function () {
        return new Error\Factory\ErrorFactory();
    };

    //
    $container['result.factory'] = function (Container $container) {
        return new Result\Factory\ResultFactory(
            $container['error.factory']
        );
    };

    //
    $container['resource_credentials.factory'] = function () {
        return new ResourceCredentials\Factory\ResourceCredentialsFactory();
    };

    $container['resource_credentials.repository.factory'] = function (Container $container) {
        return new ResourceCredentials\Repository\Factory\ResourceCredentialsRepositoryFactory(
            $container['domain_object.factory'],
            $container['resource_credentials.factory']
        );
    };

    $container['resource_credentials.repository'] = function (Container $container) {
        /** @var UnitConfig\UnitConfigInterface $unitConfig */
        $unitConfig = $container['unit_config'];
        /** @var ResourceCredentials\Repository\Factory\ResourceCredentialsRepositoryFactory $factory */
        $factory = $container['resource_credentials.repository.factory'];

        if (null !== ($path = $unitConfig->getResourceCredentialsConfigPath())) {
            return $factory->createFromFile($path);
        }

        return new ResourceCredentials\Repository\ResourceCredentialsRepository();
    };

    //
    $container['fcgi_client.result.factory'] = function (Container $container) {
        return new FcgiClient\Result\Factory\FcgiClientResultFactory($container['error.factory']);
    };

    $container['fcgi_client.adapter.adoy'] = function (Container $container) {
        return new FcgiClient\Adapter\AdoyFcgiClientAdapter($container['fcgi_client.result.factory']);
    };

    $container['fcgi_client'] = $container['fcgi_client.adapter.adoy'];

    $container['fcgi_client.command.factory'] = function (Container $container) {
        return new FcgiClient\Command\Factory\FcgiCommandFactory($container['fcgi_client']);
    };

    //
    $container['http_client.response.header.normalizer'] = function () {
        return new HttpClient\Response\Header\HttpClientResponseHeaderNormalizer();
    };

    $container['http_client.response.header.repository.factory'] = function (Container $container) {
        return new HttpClient\Response\Header\Factory\HttpClientResponseHeaderRepositoryFactory(
            $container['http_client.response.header.normalizer']
        );
    };

    $container['http_client.response.factory'] = function (Container $container) {
        return new HttpClient\Response\Factory\HttpClientResponseFactory(
            $container['http_client.response.header.repository.factory']
        );
    };

    $container['http_client.result.factory'] = function (Container $container) {
        return new HttpClient\Result\Factory\HttpClientResultFactory($container['error.factory']);
    };

    $container['http_client'] = function (Container $container) {
        return new HttpClient\HttpClient(
            $container['http_client.result.factory'],
            $container['http_client.response.factory'],
            $container['resource_credentials.repository']
        );
    };

    //
    $container['domain_object.factory'] = function () {
        return new DomainObject\Factory\DomainObjectFactory();
    };

    //
    $container['util.basic_util'] = function () {
        return new Util\BasicUtil();
    };

    $container['cmd_runner.result.factory'] = function () {
        return new CmdRunner\Result\Factory\CmdRunnerResultFactory();
    };

    $container['cmd_runner'] = function (Container $container) {
        return new CmdRunner\CmdRunner($container['cmd_runner.result.factory']);
    };

    //
    $container['archive.unarchiver_factory'] = function (Container $container) {
        return new Archive\Factory\UnarchiverFactory(
            $container['result.factory'],
            $container['cmd_runner'],
            $container['util.basic_util']
        );
    };

    //
    $container['directory_comparator.negative'] = function () {
        return new DirectoryComparator\DirectoryComparatorNegative();
    };

    $container['directory_comparator.simple'] = function () {
        return new DirectoryComparator\DirectoryComparatorSimple();
    };

    $container['directory_comparator'] = function (Container $container) {
        return $container['directory_comparator.simple'];
    };

    //
    $container['command.factory'] = function (Container $container) {
        return new Command\Factory\CommandFactory(
            $container['result.factory'],
            $container['http_client'],
            $container['archive.unarchiver_factory'],
            $container['directory_comparator'],
            $container['logger']
        );
    };

    //
    $container['etcd_client.response.node.factory'] = function (Container $container) {
        return new EtcdClient\Response\Node\Factory\EtcdClientResponseNodeFactory(
            $container['domain_object.factory']
        );
    };

    $container['etcd_client.response.factory'] = function (Container $container) {
        return new EtcdClient\Response\Factory\EtcdClientResponseFactory(
            $container['domain_object.factory'],
            $container['etcd_client.response.node.factory']
        );
    };

    $container['etcd_client.error.details.factory'] = function () {
        return new EtcdClient\Error\Details\Factory\EtcdClientErrorDetailsFactory();
    };

    $container['etcd_client.error.factory'] = function () {
        return new EtcdClient\Error\Factory\EtcdClientErrorFactory();
    };

    $container['etcd_client.result.factory'] = function (Container $container) {
        return new EtcdClient\Result\Factory\EtcdClientResultFactory(
            $container['domain_object.factory'],
            $container['etcd_client.response.factory'],
            $container['etcd_client.error.factory'],
            $container['etcd_client.error.details.factory']
        );
    };

    $container['etcd_client.server_list.factory'] = function () {
        return new EtcdClient\ServerList\Factory\EtcdClientServerListFactory();
    };

    $container['etcd_client.server_list'] = function (Container $container) {
        /** @var EtcdClient\ServerList\Factory\EtcdClientServerListFactory $factory */
        $factory = $container['etcd_client.server_list.factory'];

        /** @var UnitConfig\UnitConfigInterface $unitConfig */
        $unitConfig = $container['unit_config'];

        return $factory->makeFromString($unitConfig->getEtcdServerUrl());
    };

    $container['etcd_client'] = function (Container $container) {
        return new EtcdClient\EtcdClient(
            $container['http_client'],
            $container['etcd_client.result.factory'],
            $container['etcd_client.server_list']
        );
    };

    // fs
    $container['fs.command.factory'] = function (Container $container) {
        return new Fs\Command\Factory\FsCommandFactory($container['result.factory']);
    };

    //
    $container['runit'] = function (Container $container) {
        return new Runit\Runit(
            $container['util.basic_util'],
            $container['cmd_runner'],
            $container['result.factory']
        );
    };

    $container['runit.command.factory'] = function (Container $container) {
        return new Runit\Command\Factory\RunitCommandFactory($container['result.factory'], $container['runit']);
    };

    //
    $container['scope.config.factory'] = function () {
        return new Scope\Config\Factory\ScopeConfigFactory();
    };

    $container['scope.config.processor.rule.symlink'] = function (Container $container) {
        return new Scope\Config\Processor\Rule\ScopeConfigProcessorRuleTypeSymlinkHandler(
            $container['util.basic_util'],
            $container['result.factory'],
            $container['fs.command.factory']
        );
    };

    $container['scope.config.processor.rule.dir'] = function (Container $container) {
        return new Scope\Config\Processor\Rule\ScopeConfigProcessorRuleTypeDirHandler(
            $container['util.basic_util'],
            $container['result.factory'],
            $container['command.factory'],
            $container['fs.command.factory']
        );
    };

    $container['scope.config.processor.rule.fcgi_request'] = function (Container $container) {
        return new Scope\Config\Processor\Rule\ScopeConfigProcessorRuleTypeFcgiRequestHandler(
            $container['result.factory'],
            $container['fcgi_client.command.factory']
        );
    };

    $container['scope.config.processor.rule.runit'] = function (Container $container) {
        return new Scope\Config\Processor\Rule\ScopeConfigProcessorRuleTypeRunitHandler(
            $container['result.factory'],
            $container['runit.command.factory']
        );
    };

    //
    $container['scope.config.processor'] = function (Container $container) {
        return new Scope\Config\Processor\ScopeConfigProcessor(
            $container['logger'],
            $container['result.factory'],
            $container['command.factory'],
            $container['scope.info.factory'],
            [
                $container['scope.config.processor.rule.symlink'],
                $container['scope.config.processor.rule.dir'],
                $container['scope.config.processor.rule.fcgi_request'],
                $container['scope.config.processor.rule.runit']
            ]
        );
    };

    //
    $container['scope.info.factory'] = function () {
        return new Scope\Info\Factory\ScopeInfoFactory();
    };

    //
    $container['config.factory'] = function (Container $container) {
        return new ConfigFactory($container['domain_object.factory'], $container['scope.config.factory']);
    };

    //
    $container['unit_status_builder'] = function () {
        return new UnitStatusBuilder\UnitStatusBuilder();
    };

    //
    $container['artifact_downloader'] = function (Container $container) {
        return new ArtifactDownloader(
            $container['logger'],
            $container['unit_config'],
            $container['etcd_client'],
            $container['config.factory'],
            $container['scope.config.processor'],
            $container['unit_status_builder']
        );
    };

    return $container;
}

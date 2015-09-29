<?php

namespace {

    use CodeTool\ArtifactDownloader\Archive;
    use CodeTool\ArtifactDownloader\ArtifactDownloader;
    use CodeTool\ArtifactDownloader\Command;
    use CodeTool\ArtifactDownloader\Config\Factory\ConfigFactory;
    use CodeTool\ArtifactDownloader\DomainObject;
    use CodeTool\ArtifactDownloader\Error;
    use CodeTool\ArtifactDownloader\EtcdClient;
    use CodeTool\ArtifactDownloader\HttpClient;
    use CodeTool\ArtifactDownloader\ResourceCredentials;
    use CodeTool\ArtifactDownloader\Result;
    use CodeTool\ArtifactDownloader\UnitConfig;
    use CodeTool\ArtifactDownloader\UnitStatusBuilder;
    use CodeTool\ArtifactDownloader\Util;
    use CodeTool\ArtifactDownloader\Scope;
    use Pimple\Container;
    use Psr\Log;

    $container = new Container();

    //
    $container['logger'] = function () {
        return new Log\NullLogger();
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
    $container['resource_credentials.repository'] = function () {
        return new ResourceCredentials\Repository\ResourceCredentialsRepository();
    };

    //
    $container['http_client.response.factory'] = function () {
        return new HttpClient\Response\Factory\HttpClientResponseFactory();
    };

    $container['http_client'] = function (Container $container) {
        return new HttpClient\HttpClient(
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

    $container['util.cmd_runner.result.factory'] = function () {
        return new Util\CmdRunner\Result\Factory\CmdRunnerResultFactory();
    };

    $container['util.cmd_runner'] = function (Container $container) {
        return new Util\CmdRunner\CmdRunner($container['util.cmd_runner.result.factory']);
    };

    //
    $container['archive.unarchiver_factory'] = function (Container $container) {
        return new Archive\Factory\UnarchiverFactory(
            $container['result.factory'],
            $container['util.cmd_runner'],
            $container['util.basic_util']
        );
    };

    //
    $container['command.factory'] = function (Container $container) {
        return new Command\Factory\CommandFactory(
            $container['result.factory'],
            $container['http_client'],
            $container['archive.unarchiver_factory']
        );
    };

    //
    $container['etcd_client.error.factory'] = function () {
        return new EtcdClient\Error\Factory\EtcdClientErrorFactory();
    };

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

    $container['etcd_client.result.factory'] = function (Container $container) {
        return new EtcdClient\Result\Factory\EtcdClientResultFactory(
            $container['domain_object.factory'],
            $container['etcd_client.error.factory'],
            $container['etcd_client.response.factory']
        );
    };

    $container['etcd_client'] = function (Container $container) {
        return new EtcdClient\EtcdClient(
            $container['http_client'],
            $container['etcd_client.result.factory']
        );
    };

    //
    $container['scope.config.factory'] = function () {
        return new Scope\Config\Factory\ScopeConfigFactory();
    };

    //
    $container['scope.info.factory'] = function () {
        return new Scope\Info\Factory\ScopeInfoFactory();
    };

    //
    $container['scope.state.type_handler.file_dir'] = function (Container $container) {
        return new Scope\State\TypeHandler\ScopeStateFileDirTypeHandler(
            $container['util.basic_util'],
            $container['command.factory']
        );
    };

    $container['scope.state.type_handler.symlink'] = function (Container $container) {
        return new Scope\State\TypeHandler\ScopeStateSymlinkTypeHandler(
            $container['util.basic_util'],
            $container['command.factory']
        );
    };

    //
    $container['scope.state_builder'] = function (Container $container) {
        return new Scope\State\ScopeStateBuilder(
            $container['command.factory'],
            $container['scope.info.factory'],
            [
                $container['scope.state.type_handler.symlink'],
                $container['scope.state.type_handler.file_dir'],
            ]
        );
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
    $container['unit_config'] = function () {
        return new UnitConfig\UnitConfig();
    };

    //
    $container['artifact_downloader'] = function (Container $container) {
        return new ArtifactDownloader(
            $container['logger'],
            $container['unit_config'],
            $container['etcd_client'],
            $container['config.factory'],
            $container['scope.state_builder'],
            $container['unit_status_builder']
        );
    };

    return $container;
}

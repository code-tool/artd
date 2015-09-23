<?php

namespace {
    use Pimple\Container;
    use Psr\Log;
    use CodeTool\ArtifactDownloader\ResourceCredentials;
    use CodeTool\ArtifactDownloader\HttpClient;
    use CodeTool\ArtifactDownloader\Command;
    use CodeTool\ArtifactDownloader\DomainObject;
    use CodeTool\ArtifactDownloader\EtcdClient;


    $container = new Container();

    //
    $container['logger'] = function () {
        return new Log\NullLogger();
    };

    //
    $container['resource_credentials.repository'] = function () {
        return new ResourceCredentials\Repository\ResourceCredentialsRepository();
    };

    //
    $container['http_client.response.factory'] = function () {
        return new HttpClient\Response\Factory\HttpClientResponseFactory();
    };

    $container['http_client'] = function(Container $container) {
        return new HttpClient\HttpClient(
            $container['http_client.response.factory'],
            $container['resource_credentials.repository']
        );
    };

    //
    $container['command.result.factory'] = function () {
        return new Command\Result\Factory\CommandResultFactory();
    };

    $container['command.factory'] = function (Container $container) {
        return new Command\Factory\CommandFactory(
            $container['command.result.factory'],
            $container['http_client']
        );
    };

    //
    $container['domain_object.factory'] = function () {
        return new DomainObject\Factory\DomainObjectFactory();
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

    return $container;
}

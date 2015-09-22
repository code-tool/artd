<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigChildNode;

$logger = new \Psr\Log\NullLogger();
$resourceCredentialsRepository =
    new \CodeTool\ArtifactDownloader\ResourceCredentials\Repository\ResourceCredentialsRepository();

$commandResultFactory = new \CodeTool\ArtifactDownloader\Command\Result\Factory\CommandResultFactory();
$commandFactory = new \CodeTool\ArtifactDownloader\Command\Factory\CommandFactory(
    $commandResultFactory,
    $resourceCredentialsRepository
);

$scopeConfig = new \CodeTool\ArtifactDownloader\Scope\Config\ScopeConfig(
    __DIR__ . '/../test',
    [
        new ScopeConfigChildNode('dir1', 'directory', null, null, null, 0755),
        //new ScopeConfigChildNode('dir4', 'directory', 'https://github.com/php-fig/log/archive/1.0.0.tar.gz', '0b54f47edad44c0e32847292863a3204c4c3a04ae68448b73b01b3b6c51733ab', null, 0755),
        new ScopeConfigChildNode('dir3', 'directory', 'https://github.com/php-fig/log/archive/1.0.0.zip', 'dc212cda14de5b1db56a1fed92e0944585182d20cd1d026e5bb652f589197ba0', null, 0755),
        new ScopeConfigChildNode('shared/log', 'directory', null, null, null, 0755),
        //new ScopeConfigChildNode('dir1/log', 'symlink', null, null, 'shared/log', 0755),
    ]
);

$syncer = new \CodeTool\ArtifactDownloader\Scope\State\ScopeStateSynchronizer($commandFactory, $logger, $scopeConfig);
var_dump($syncer->sync());

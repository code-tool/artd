<?php

require_once __DIR__ . '/../vendor/autoload.php';
$container = require_once __DIR__ . '/../src/container.php';

/** @var \CodeTool\ArtifactDownloader\ArtifactDownloader $artifactDownloader */
$artifactDownloader = $container['artifact_downloader'];

try {
    $returnCode = $artifactDownloader->work(true);
} catch (Exception $e) {
    $returnCode = 1;
    echo $e->getMessage(), PHP_EOL, $e->getTraceAsString();
}

exit($returnCode);

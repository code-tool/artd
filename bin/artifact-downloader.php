<?php

require_once __DIR__ . '/../vendor/autoload.php';
$container = require_once __DIR__ . '/../src/container.php';

// Fix warnings on old curl
if (false === defined('CURLOPT_TCP_KEEPALIVE')) {
    define('CURLOPT_TCP_KEEPALIVE', 213);
}

if (false === defined('CURLOPT_TCP_KEEPIDLE')) {
    define('CURLOPT_TCP_KEEPIDLE', 214);
}

if (false === defined('CURLOPT_TCP_KEEPINTVL')) {
    define('CURLOPT_TCP_KEEPINTVL', 215);
}

/** @var \CodeTool\ArtifactDownloader\UnitConfig\UnitConfigInterface $unitConfig */
$unitConfig = $container['unit_config'];
/** @var \CodeTool\ArtifactDownloader\ArtifactDownloader $artifactDownloader */
$artifactDownloader = $container['artifact_downloader'];

try {
    $returnCode = $artifactDownloader->work(false === $unitConfig->getIsApplyOnceMode());
} catch (Exception $e) {
    $returnCode = 1;
    echo $e->getMessage(), PHP_EOL, $e->getTraceAsString();
}

exit($returnCode);

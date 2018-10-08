<?php

require_once __DIR__ . '/../root.php';

$pharFile = __DIR__ . '/../build/yml-downloader.phar';
$version = YMDL_VERSION;

if (file_exists($pharFile)) {
    echo 'Old file size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
    unlink($pharFile);
}

$phar = new Phar($pharFile);
$phar->setMetadata(['version' => $version, 'date' => (new DateTime())->format('c')]);
$phar->buildFromDirectory(dirname(__DIR__), '/(src|vendor|(console|root)\.php)/');
$phar->setStub($phar->createDefaultStub('console.php'));

echo 'File size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;

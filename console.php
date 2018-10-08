<?php

use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

// Init and run Symfony Console
$console = new Application('YML-Downloader', '0.1');
$console->add(new DownloadCommand());
$console->run();

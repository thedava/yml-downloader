<?php

use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

// Init and run Symfony Console
$console = new Application('DavaHome.NET');
$console->add(new DownloadCommand());
$console->run();

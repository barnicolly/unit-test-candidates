#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Console\Application;

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$application = $container->get(Application::class);
$application->run();

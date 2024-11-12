#!/usr/bin/env php
<?php

namespace Bin;

use App\Bootstrap;
use Stepapo\Utils\Printer;
use Tester\Runner\CliTester;
use Webovac\Core\Command\MigrateCommand;
use Webovac\Core\Command\NewMigrateCommand;

require __DIR__ . '/../vendor/autoload.php';


# BUILD TEST DATABASE
$_SERVER['argv'][] = '--reset';
exec(__DIR__ . '/clear-cache');
Bootstrap::boot(testMode: true)->createContainer()->getByType(MigrateCommand::class)->run();
exec(__DIR__ . '/clear-cache');

# RUN TESTS
$j = (int) shell_exec('nproc');
$argv = [
	'tester',
	'--setup=tests/setup.php',
	'-o=none',
	'-c=tests/php.ini',
	'--coverage=tests/coverage.html',
	'--coverage-src=lib/webovac',
	"-j=$j",
	'--cider',
	'tests'
];
$_SERVER['argv'] = $argv;
$_SERVER['argc'] = count($argv);
(new CliTester)->run();
exec(__DIR__ . '/clear-cache');

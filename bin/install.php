<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Core\Command\MigrateAndInstallCommand;


exec(__DIR__ . '/clear-cache');
Bootstrap::boot()
	->createContainer()
	->getByType(MigrateAndInstallCommand::class)
	->run();
exec(__DIR__ . '/clear-cache');

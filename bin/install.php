<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Core\Command\InstallCommand;
use Webovac\Core\Command\MigrateCommand;


exec(__DIR__ . '/clear-cache');
Bootstrap::boot()->createContainer()->getByType(MigrateCommand::class)->run();
exec(__DIR__ . '/clear-cache');
Bootstrap::boot()->createContainer()->getByType(InstallCommand::class)->run();
exec(__DIR__ . '/clear-cache');

<?php

namespace Bin;

use App\Bootstrap;
use Webovac\Core\Command\MigrateCommand;

require __DIR__ . '/../vendor/autoload.php';


exec(__DIR__ . '/clear-cache');
Bootstrap::boot()->createContainer()->getByType(MigrateCommand::class)->run();
exec(__DIR__ . '/clear-cache');

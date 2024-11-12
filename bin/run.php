<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Core\Lib\CommandRunner;


$runner = Bootstrap::boot()->createContainer()->getByType(CommandRunner::class);

if (!isset($_SERVER['argv'][1])) {
	$runner->printCommands();
} else {
	$runner->run($_SERVER['argv'][1]);
}

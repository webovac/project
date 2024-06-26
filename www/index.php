<?php

declare(strict_types=1);

namespace Www;

use App\Bootstrap;
use Nette\Application\Application;

require __DIR__ . '/../vendor/autoload.php';


Bootstrap::boot()
	->createContainer()
	->getByType(Application::class)
	->run();

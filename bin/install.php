<?php

namespace Bin;

use App\Bootstrap;
use Nette\Utils\FileInfo;
use Nette\Utils\Finder;
use Stepapo\Model\Definition\PropertyProcessor;
use Tester\Runner\CliTester;
use Webovac\Core\Command\MigrateCommand;
use Webovac\Core\Lib\RegisterOrmEvents;
use Webovac\Generator\CmsGenerator;
use Webovac\Generator\Lib\Processor;

require __DIR__ . '/../vendor/autoload.php';


# GENERATE FILES
$generator = new CmsGenerator;
$fileProcessor = new Processor($generator);
$files = Finder::findDirectories("*config/files")->from(__DIR__ . '/../')->collect();
$fileProcessor->process(
	folders: array_map(fn(FileInfo $f) => $f->getPathname(), $files),
	appDir: __DIR__ . '/../app',
	buildDir: __DIR__ . '/../build',
);

# GENERATE ENTITY PROPERTIES
$propertyProcessor = new PropertyProcessor(['defaultSchema' => 'webovac'], $generator);
$definitions = Finder::findDirectories("*config/definitions")->from(__DIR__ . '/../')->collect();
$propertyProcessor->process(
	folders: array_map(fn(FileInfo $f) => $f->getPathname(), $definitions)
);

# MIGRATE DB
exec(__DIR__ . '/clear-cache');
$command = Bootstrap::boot()->createContainer()->getByType(MigrateCommand::class);
$command->runDefinitions();
exec(__DIR__ . '/clear-cache');
$container = Bootstrap::boot()->createContainer();
$container->getByType(RegisterOrmEvents::class)->register();
$container->getByType(MigrateCommand::class)->runManipulations();
exec(__DIR__ . '/clear-cache');
$command = Bootstrap::boot()->createContainer()->getByType(MigrateCommand::class);
$command->runMigrations();
exec(__DIR__ . '/clear-cache');

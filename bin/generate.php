<?php

namespace Bin;

use Nette\Utils\FileInfo;
use Nette\Utils\Finder;
use Stepapo\Model\Definition\PropertyProcessor;
use Webovac\Generator\CmsGenerator;
use Webovac\Generator\Lib\Processor;

require __DIR__ . '/../vendor/autoload.php';

foreach ($_SERVER['argv'] as $arg) {
	if ($arg === '--reset') {
		exec(__DIR__ . '/clear-build');
	}
}

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
$propertyProcessor = new PropertyProcessor(['defaultSchema' => 'public'], $generator);
$definitions = Finder::findDirectories("*config/definitions")->from(__DIR__ . '/../')->collect();
$propertyProcessor->process(
	folders: array_map(fn(FileInfo $f) => $f->getPathname(), $definitions)
);

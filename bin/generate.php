<?php

namespace Bin;

use Nette\Utils\FileInfo;
use Nette\Utils\Finder;
use Stepapo\Model\Definition\PropertyProcessor;
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
);

# GENERATE ENTITY PROPERTIES
$propertyProcessor = new PropertyProcessor($generator);
$definitions = Finder::findDirectories("*config/definitions")->from(__DIR__ . '/../')->collect();
$propertyProcessor->process(
	folders: array_map(fn(FileInfo $f) => $f->getPathname(), $definitions)
);
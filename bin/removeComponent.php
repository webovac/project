<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:', 'module:', 'entityName:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->removeCmsComponent(
	name: $options['name'],
	module: $options['module'] ?? null,
	entityName: $options['entityName'] ?? null,
);

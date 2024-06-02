<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:', 'module:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->removeCmsModel(
	name: $options['name'],
	module: $options['module'] ?? null,
);

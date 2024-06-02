<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:', 'module:', 'withConventions:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->createCmsModel(
	name: $options['name'],
	module: $options['module'] ?? null,
	withConventions: $options['withConventions'] ?? false,
);

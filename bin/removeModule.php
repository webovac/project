<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->removeModule(
	name: $options['name'],
);

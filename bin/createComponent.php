<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Stepapo\Generator\ComponentGenerator;
use Webovac\Core\Lib\Dataset\CmsDatasetFactory;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:', 'module:', 'type:', 'entityName:', 'withTemplateName:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->createCmsComponent(
	name: $options['name'],
	module: $options['module'] ?? 'App',
	entityName: $options['entityName'] ?? null,
	withTemplateName: $options['withTemplateName'] ?? false,
	type: $type = $options['type'] ?? null,
	factory: match ($type) {
		ComponentGenerator::TYPE_DATASET => CmsDatasetFactory::class,
		default => null,
	},
);

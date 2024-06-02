<?php

namespace Bin;

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Webovac\Generator\CmsGenerator;


$options = getopt(null, ['name:', 'withModel:', 'withDIExtension:', 'withMigrationGroup:', 'withInstallGroups:', 'type:']);
Bootstrap::boot()->createContainer()->getByType(CmsGenerator::class)->createModule(
	name: $options['name'],
	withModel: $options['withModel'] ?? false,
	withDIExtension: $options['withDIExtension'] ?? false,
	withMigrationGroup: $options['withMigrationGroup'] ?? false,
	withInstallGroups: $options['withInstallGroups'] ?? false,
	type: $options['type'] ?? 'module',
);

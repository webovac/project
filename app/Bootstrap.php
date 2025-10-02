<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot($testMode = false): Configurator
	{
		$rootDir = dirname(__DIR__);
		$configurator = (new Configurator)
			->setTempDirectory($rootDir . '/temp')
			->addConfig($rootDir . '/config/common.neon')
			->addConfig($rootDir . '/config/local.neon');
		if ($testMode) {
			$configurator->addConfig($rootDir . '/config/test.neon');
		}
		$debugMode = !$testMode && getenv('NETTE_DEVEL') === '1';
		if ($debugMode || $testMode) {
			$configurator
				->createRobotLoader()
				->addDirectory($rootDir . '/app')
				->addDirectory($rootDir . '/build')
				->addDirectory($rootDir . '/lib')
				->register();
		}
		$configurator
			->setDebugMode($debugMode)
//			->setDebugMode(true)
			->enableTracy($rootDir . '/log');
		return $configurator;
	}
}

<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$appDir = dirname(__DIR__);

		$configurator = (new Configurator)
			->setTempDirectory($appDir . '/temp')
			->addConfig($appDir . '/config/common.neon')
			->addConfig($appDir . '/config/local.neon');

		$configurator
			->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->setDebugMode(getenv('NETTE_DEVEL') === '1');
		$configurator->enableTracy($appDir . '/log');

		return $configurator;
	}
}

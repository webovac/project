<?php

namespace App\Router;

use App\Language;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Routing\Router;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Webovac\Core\Router\CmsRouterFactory;


final class RouterFactory
{
	public function __construct(
		private CmsRouterFactory $cmsRouterFactory,
	) {}


	public function create(): Router
	{
		$router = new RouteList;
		$router->add($this->cmsRouterFactory->create());
		return $router;
	}
}

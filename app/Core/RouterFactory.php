<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('', 'Home:default');
		$router->addRoute('chuck', 'Home:chuck');
		$router->addRoute('initials', 'Home:initials');
		$router->addRoute('dates', 'Home:dates');
		$router->addRoute('eval', 'Home:eval');
		$router->addRoute('calculations', 'Home:calculation');

		return $router;
	}
}

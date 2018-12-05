<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory {
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter() {
		$router = new RouteList;
        $router[] = new Route('login', 'Sign:in');
        $router[] = new Route('logout', 'Sign:out');
        $router[] = new Route('schedule?teacher=<teacherId>&room=<roomId>&semester=<semesterId>&plan=<planId>&year=<yearId>', 'Schedule:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}

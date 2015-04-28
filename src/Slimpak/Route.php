<?php namespace Slimpak;

class Route extends \SlimFacades\Route {
	/**
	 * Adding route resource like a Laravel Frameworks.
	 * @param  string $url
	 * @param  string $controller
	 * @return string
	 */
	public static function resource($url, $controller)
	{
		$route = array(
			$url 				=> array('get' => $controller.':index', 'post' => $controller.':store'),
			$url.'/create' 		=> array('get' => $controller.':create'),
			$url.'/:id/edit' 	=> array('get' => $controller.':edit'),
			$url.'/:id'			=> array('put' => $controller.':update', 'delete' => $controller.':delete')
		);

		return static::$slim->addRoutes($route);
	}
}
<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Application.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
class Application implements ApplicationInterface {

	protected $controllers;
	protected $router;
	protected $request;
	protected $response;

	/**
	 * constructor. connects to db and starts the session
	 */
	function __construct(ControllerFactoryInterface $controllers, RouterInterface $router, Response $response) {
		$this->controllers = $controllers;
		$this->router = $router;
		$this->response = $response;
	}

	public function handle(Request $request) {
		$this->response->assign("request", $request);
		$route = $this->router->route($request);
		foreach ($route as $k => $v) {
			$this->response->{$k} = $v;
		}
		$controller = $this->controllers->get($route['controller']);

		if (isset($controller->validators[$route['action']])) {
			$template = $controller->validators[$route['action']];
		}

		$controller->start($request, $this->response);
		$controller->action($route['action'], $route['arguments']);
		$this->response = $controller->finish();
		return $this->response;
	}
}

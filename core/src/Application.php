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
	protected $dispatcher;
	protected $router;
	protected $request;
	protected $response;

	/**
	 * constructor. connects to db and starts the session
	 */
	function __construct(ControllerFactoryInterface $controllers, RouterInterface $router, EventDispatcher $dispatcher, Response $response) {
		$this->controllers = $controllers;
		$this->dispatcher = $dispatcher;
		$this->router = $router;
		$this->response = $response;
	}

	public function handle(Request $request) {
		$response->assign("request", $request);
		$route = $this->router->route($request);
		$controller = $this->controllers->get($route['controller']);
		$controller->start($request, $response);
		$controller->action($route['action'], $route['arguments']);
		$this->response = $controller->finish();
		return $response;
	}
}

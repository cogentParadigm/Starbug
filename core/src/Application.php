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
	protected $config;
	protected $locator;

	/**
	 * constructor. connects to db and starts the session
	 */
	function __construct(ControllerFactoryInterface $controllers, RouterInterface $router, ConfigInterface $config, ResourceLocatorInterface $locator, Response $response) {
		$this->controllers = $controllers;
		$this->router = $router;
		$this->config = $config;
		$this->locator = $locator;
		$this->response = $response;
	}

	public function handle(Request $request) {
		$this->response->assign("request", $request);
		$route = $this->router->route($request);

		if (empty($route['theme'])) $route['theme'] = $this->config->get("theme", "settings");
		if (empty($route['layout'])) $route['layout'] = empty($route['type']) ? "views" : $route['type'];
		if (empty($route['template'])) $route['template'] = $request->format;
		$this->locator->set("theme", "app/themes/".$route['theme']);

		foreach ($route as $k => $v) {
			$this->response->{$k} = $v;
		}
		$controller = $this->controllers->get($route['controller']);

		if (isset($controller->validators[$route['action']])) {
			$template = $controller->validators[$route['action']];
		}

		if (empty($route['arguments'])) $route['arguments'] = array();

		$controller->start($request, $this->response);
		$controller->action($route['action'], $route['arguments']);
		$this->response = $controller->finish();
		return $this->response;
	}
}

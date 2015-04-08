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
	protected $models;
	protected $db;
	protected $router;
	protected $request;
	protected $response;
	protected $config;
	protected $locator;

	/**
	 * constructor. connects to db and starts the session
	 */
	public function __construct(
		ControllerFactoryInterface $controllers,
		ModelFactoryInterface $models,
		DatabaseInterface $db,
		RouterInterface $router,
		SettingsInterface $settings,
		ResourceLocatorInterface $locator,
		Response $response
	) {
		$this->controllers = $controllers;
		$this->models = $models;
		$this->db = $db;
		$this->router = $router;
		$this->settings = $settings;
		$this->locator = $locator;
		$this->response = $response;
	}

	public function handle(Request $request) {
		$this->response->assign("request", $request);
		$route = $this->router->route($request);

		if (empty($route['theme'])) $route['theme'] = $this->settings->get("theme");
		if (empty($route['layout'])) $route['layout'] = empty($route['type']) ? "views" : $route['type'];
		if (empty($route['template'])) $route['template'] = $request->format;
		$this->locator->set("theme", "app/themes/".$route['theme']);

		foreach ($route as $k => $v) {
			$this->response->{$k} = $v;
		}
		$controller = $this->controllers->get($route['controller']);

		if (isset($controller->routes[$route['action']])) {
			$template = $controller->routes[$route['action']];
			if (false === ($values = $this->router->validate($request, $route, $template))) {
				$route['action'] = 'missing';
			} else if (is_array($values)) {
				$route['arguments'] = $values;
			}
		}

		if (empty($route['arguments'])) $route['arguments'] = array();

		$controller->start($request, $this->response);
		$permitted = $this->check_post($request->data, $request->cookies);
		if ($permitted) $controller->action($route['action'], $route['arguments']);
		else $controller->forbidden();
		$this->response = $controller->finish();
		return $this->response;
	}
	/**
	* run a model action if permitted
	* @param string $key the model name
	* @param string $value the function name
	*/
	protected function post_action($key, $value, $post=null) {
		if ($object = $this->models->get($key)) {
			error_scope($key);
			if (isset($post['id'])) {
				$permits = $this->db->query($key)->action($value)->condition($key.".id", $post['id'])->one();
			} else {
				$permits = $this->db->query("permits")->action($value, $key)->one();
			}
			if ($permits) $object->$value($post);
			else return false;
			error_scope("global");
			return true;
		}
	}

	/**
	* check $_POST['action'] for posted actions and run them through post_act
	*/
	protected function check_post($post, $cookies) {
		if (!empty($post['action'])) {
			//validate csrf token for authenticated requests
			if (logged_in()) {
				$validated = false;
				if (!empty($cookies['oid']) && !empty($post['oid']) && $cookies['oid'] === $post['oid']) $validated = true;
				if (true !== $validated) {
					return false;
				}
			}
			//execute post actions
			foreach ($post['action'] as $key => $val) return $this->post_action(normalize($key), normalize($val), $post[$key]);
		}
		return true;
	}
}

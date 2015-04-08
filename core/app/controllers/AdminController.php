<?php
class AdminController {
	function __construct(ControllerFactoryInterface $controllers, RouterInterface $router) {
		$this->controllers = $controllers;
		$this->router = $router;
	}
	function default_action() {
		$name = $this->request->uri[1];
		$controller = $this->controllers->get("Admin".ucwords($name));
		$controller->start($this->request, $this->response);
		$action = $this->request->uri[2];
		$arguments = array();
		if (isset($controller->routes[$action])) {
			$template = $controller->routes[$action];
			if (false === ($values = $this->router->validate($this->request, array('path' => 'admin/'.$name), $template))) {
				$action = 'missing';
			} else if (is_array($values)) {
				$arguments = $values;
			}
		}
		$controller->action($action, $arguments);
		$this->response = $controller->finish();
	}
}
?>

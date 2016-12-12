<?php
namespace Starbug\Core;
use Starbug\Core\Routing\RouterInterface;
class AdminController extends Controller {
	function __construct(ControllerFactoryInterface $controllers, RouterInterface $router) {
		$this->controllers = $controllers;
		$this->router = $router;
	}
	function default_action() {
		if (count($this->request->getComponents()) == 1) {
			return $this->render("admin");
		}
		$name = $this->request->getComponent(1);
		$controller = $this->controllers->get("Admin".ucwords($name));
		$controller->start($this->request, $this->response);
		$action = $this->request->getComponent(2);
		$arguments = array();
		if (isset($controller->routes[$action])) {
			$template = $controller->routes[$action];
			if (false === ($values = $this->router->validate($this->request, array('path' => 'admin/'.$name.'/'.$action), $template))) {
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

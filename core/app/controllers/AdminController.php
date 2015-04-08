<?php
class AdminController {
	function __construct(ControllerFactoryInterface $controllers) {
		$this->controllers = $controllers;
		$this->init();
	}
	function default_action() {
		$controller = $this->controllers->get("Admin".ucwords($this->request->uri[1]));
		$controller->start($this->request, $this->response);
		$controller->action($this->request->uri[2]);
	}
}
?>

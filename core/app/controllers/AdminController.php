<?php
class AdminController {
	function response($args) {
		$controller = $this->controllers->get("Admin".ucwords($args[0]));
		$controller->start($this->request, $this->response);
	}
}
?>

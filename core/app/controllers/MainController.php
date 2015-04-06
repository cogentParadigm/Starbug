<?php
class MainController {
	function missing() {
		$this->response->missing();
		$this->render("missing");
	}
	function forbidden() {
		$this->response->forbidden();
		$this->render("forbidden");
	}
}
?>

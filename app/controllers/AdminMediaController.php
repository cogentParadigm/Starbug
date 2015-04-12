<?php
class AdminMediaController {
	public $routes = array(
		'update' => '{id}'
	);
	function init() {
		$this->assign("model", "files");
	}
	function default_action() {
		$this->response->template = "media-browser";
	}
	function update($id) {
		$this->assign("id", $id);
		$this->assign("action", "update");
		$this->render("admin/update");
	}
}
?>

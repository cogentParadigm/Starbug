<?php
class AdminMediaController {
	function init() {
		$this->assign("model", "files");
	}
	function default_action() {
		$this->response->template = "media-browser";
	}
	function update($id=null) {
		$this->assign("id", $id);
		$this->assign("action", "update");
		$this->render("admin/update");
	}
}
?>

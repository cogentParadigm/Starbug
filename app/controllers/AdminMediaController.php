<?php
class AdminMediaController {
	function init() {
		assign("model", "files");
	}
	function default_action() {
		$this->template = "media-browser";
	}
	function update($id=null) {
		assign("id", $id);
		assign("action", "update");
		$this->render("admin/update");
	}
}
?>

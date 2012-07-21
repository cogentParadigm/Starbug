<?php
class AdminUrisController extends Controller {
	function init() {
		assign("model", "uris");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		//autoloads admin/views/uris/create.php
	}
	function update($id=null) {
		assign("id", $id);
		//autoloads admin/views/uris/update.php
	}
}
?>

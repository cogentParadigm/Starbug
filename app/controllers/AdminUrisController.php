<?php
class AdminUrisController {
	function init() {
		assign("model", "uris");
	}
	function default_action() {
		assign("columns", array(
			"Statuses" => "field:'statuses'  plugin:starbug.grid.columns.terms  taxonomy:'statuses'"
		));
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

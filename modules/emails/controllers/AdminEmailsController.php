<?php
class AdminEmailsController {
	function init() {
		assign("model", "email_templates");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("email_templates", "create")) redirect(uri("admin/email_templates/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/update");
	}
}
?>

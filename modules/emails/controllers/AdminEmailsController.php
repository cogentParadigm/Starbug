<?php
class AdminEmailsController {
	function init() {
		$this->assign("model", "email_templates");
		$this->assign("cancel_url", "admin/emails");
		if (success("email_templates", "create")) redirect(uri("admin/emails", 'u'));
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->render("admin/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		$this->render("admin/update");
	}
}
?>

<?php
class AdminPostsController {
	function init() {
		assign("model", "posts");
		assign("cancel_url", "admin/posts");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("posts", "create")) redirect(uri("admin/posts", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		if (success("posts", "create")) redirect(uri("admin/posts", 'u'));
		else $this->render("admin/update");
	}
}
?>
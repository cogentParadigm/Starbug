<?php
class AdminPostsController {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "posts");
		$this->assign("cancel_url", "admin/posts");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("posts", "create")) redirect(uri("admin/posts", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("posts", "create")) redirect(uri("admin/posts", 'u'));
		else $this->render("admin/update");
	}
}
?>

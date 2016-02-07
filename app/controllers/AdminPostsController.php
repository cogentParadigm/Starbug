<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminPostsController extends Controller {
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
		if ($this->db->success("posts", "create")) $this->redirect("admin/posts");
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("posts", "create")) $this->redirect("admin/posts");
		else $this->render("admin/update");
	}
}
?>

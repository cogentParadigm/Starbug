<?php
namespace Starbug\Emails;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminEmailsController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "email_templates");
		$this->assign("cancel_url", "admin/emails");
		if ($this->db->success("email_templates", "create")) $this->redirect("admin/emails");
	}
	function default_action() {
		$this->render("admin/list.html");
	}
	function create() {
		$this->render("admin/create.html");
	}
	function update($id) {
		$this->assign("id", $id);
		$this->render("admin/update.html");
	}
}

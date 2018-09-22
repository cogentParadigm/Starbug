<?php
namespace Starbug\App;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminUsersController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "users");
    $this->assign("cancel_url", "admin/users");
  }
  public function default_action() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("users", "create")) $this->redirect("admin/users");
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}

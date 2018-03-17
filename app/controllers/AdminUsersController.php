<?php
namespace Starbug\App;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminUsersController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "users");
    $this->assign("cancel_url", "admin/users");
  }
  function default_action() {
    $this->render("admin/list.html");
  }
  function create() {
    if ($this->db->success("users", "create")) $this->redirect("admin/users");
    else $this->render("admin/create.html");
  }
  function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
  function import() {
    $this->render("admin/import.html");
  }
}

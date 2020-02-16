<?php
namespace Starbug\Users;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminUsersController extends Controller {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "users");
  }
  public function defaultAction() {
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

<?php
namespace Starbug\Content;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminPagesController extends Controller {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "pages");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("pages", "create")) $this->redirect("admin/pages");
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("pages", "create")) $this->redirect("admin/pages");
    else $this->render("admin/update.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}

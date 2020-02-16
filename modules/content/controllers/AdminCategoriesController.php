<?php
namespace Starbug\Content;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminCategoriesController extends Controller {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "categories");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("categories", "create")) {
      $this->redirect("admin/categories");
    } else {
      $this->render("admin/create.html");
    }
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("categories", "create")) {
      $this->redirect("admin/categories");
    } else {
      $this->render("admin/update.html");
    }
  }
  public function import() {
    $this->render("admin/import.html");
  }
}

<?php
namespace Starbug\Core;

class AdminImportsFieldsController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "imports_fields");
    $this->assign("cancel_url", "admin/imports_fields");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
}

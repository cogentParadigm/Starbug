<?php
namespace Starbug\Core;

class AdminImportsController extends Controller {
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
    $this->db = $db;
    $this->models = $models;
  }
  public function init() {
    $this->assign("model", "imports");
    $this->assign("cancel_url", "admin/imports");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("imports", "create")) $this->redirect("admin/imports/update/".$this->models->get("imports")->insert_id);
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $import = $this->models->get("imports")->load($id);
    if ($this->db->success("imports", "create")) $this->redirect("admin/".$import['model']."/import");
    else $this->render("admin/update.html");
  }
  public function run($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html", ["form_header" => "Run Import", "action" => "run"]);
  }
}

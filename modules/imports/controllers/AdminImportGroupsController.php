<?php
namespace Starbug\Spreadsheet;

use Starbug\Db\DatabaseInterface;
use Starbug\Core\Controller;

class AdminImportGroupsController extends Controller {
  public $routes = [
    'update' => '{id}',
    'run' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "import_groups");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("import_groups", "create")) {
      $this->redirect("admin/import_groups");
    } else {
      $this->render("admin/create.html");
    }
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("import_groups", "create")) {
      $this->redirect("admin/import_groups");
    } elseif ($this->db->success("import_groups", "saveRun")) {
      $this->redirect("admin/import_groups/run/".$id);
    } else {
      $this->render("admin/update.html");
    }
  }
  public function run($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html", ["form_header" => "Run Import Group", "action" => "run"]);
  }
}

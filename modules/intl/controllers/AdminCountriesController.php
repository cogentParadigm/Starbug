<?php
namespace Starbug\Intl;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminCountriesController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "countries");
  }
  public function default_action() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("countries", "create")) $this->redirect("admin/countries");
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("countries", "create")) $this->redirect("admin/countries");
    else $this->render("admin/update.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}

<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminProductTypesController extends Controller {
  public $routes = array(
    'update' => '{id}'
  );
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "product_types");
  }
  function default_action() {
    $this->render("admin/list");
  }
  function create() {
    if ($this->db->success("product_types", "create")) $this->redirect("admin/product_types");
    else $this->render("admin/create");
  }
  function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("product_types", "create")) $this->redirect("admin/product_types");
    else $this->render("admin/product_types/update");
  }
}

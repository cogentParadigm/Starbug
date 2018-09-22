<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminProductsController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "products");
  }
  public function default_action() {
    $this->render("admin/list");
  }
  public function create() {
    if ($this->db->success("products", "create")) $this->redirect("admin/products");
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("products", "create")) $this->redirect("admin/products");
    else $this->render("admin/update.html");
  }
}

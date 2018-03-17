<?php

namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminProductOptionsController extends Controller {
  public $routes = array(
    'update' => '{id}'
  );
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "product_options");
  }
  function default_action() {
    $this->render("admin/list.html");
  }
  function create() {
    $this->render("admin/create.html");
  }
  function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
}
?>
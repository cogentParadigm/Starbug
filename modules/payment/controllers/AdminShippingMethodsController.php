<?php

namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminShippingMethodsController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "shipping_methods");
    $this->assign("cancel_url", "admin/shipping_methods");
  }
  function default_action() {
    $this->render("admin/list.html");
  }
  function create() {
    if ($this->db->success("shipping_methods", "create")) $this->redirect("admin/shipping_methods");
    else $this->render("admin/create.html");
  }
  function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("shipping_methods", "create")) $this->redirect("admin/shipping_methods");
    else $this->render("admin/update.html");
  }
}

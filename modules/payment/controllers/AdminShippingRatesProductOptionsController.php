<?php

namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminShippingRatesProductOptionsController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "shipping_rates_product_options");
  }
  function default_action() {
    $this->render("admin/list");
  }
  function create() {
    $this->render("admin/create");
  }
  function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update");
  }
}

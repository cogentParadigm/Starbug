<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminPaymentGatewaysController extends Controller {
  public $routes = array(
    'update' => '{id}',
    'settings' => '{id}'
  );
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "payment_gateways");
  }
  function default_action() {
    $this->render("admin/list.html");
  }
  function create() {
    if ($this->db->success("payment_gateways", "create")) $this->redirect("admin/payment_gateways");
    else $this->render("admin/create.html");
  }
  function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("payment_gateways", "create")) $this->redirect("admin/payment_gateways");
    else $this->render("admin/update.html");
  }
  function import() {
    $this->render("admin/import.html");
  }
  function settings($id) {
    $gateway = $this->db->query("payment_gateways")->condition("id", $id)->one();
    $this->assign("gateway", $gateway);
    $this->assign("model", "payment_gateway_settings");
    $this->render("admin/payment_gateways/settings.html");
  }
}

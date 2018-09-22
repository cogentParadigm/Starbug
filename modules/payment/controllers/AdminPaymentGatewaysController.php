<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminPaymentGatewaysController extends Controller {
  public $routes = [
    'update' => '{id}',
    'settings' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "payment_gateways");
  }
  public function default_action() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("payment_gateways", "create")) $this->redirect("admin/payment_gateways");
    else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("payment_gateways", "create")) $this->redirect("admin/payment_gateways");
    else $this->render("admin/update.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
  public function settings($id) {
    $gateway = $this->db->query("payment_gateways")->condition("id", $id)->one();
    $this->assign("gateway", $gateway);
    $this->assign("model", "payment_gateway_settings");
    $this->render("admin/payment_gateways/settings.html");
  }
}

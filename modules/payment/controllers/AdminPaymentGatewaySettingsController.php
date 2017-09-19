<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminPaymentGatewaySettingsController extends Controller {
  public $routes = array(
    'update' => '{id}'
  );
  function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function init() {
    $this->assign("model", "payment_gateway_settings");
  }
  function default_action() {
    $this->render("admin/list");
  }
  function create() {
    if ($this->db->success("payment_gateway_settings", "create")) {
      $this->redirect("admin/payment_gateways/settings/".$this->request->getParameter("gateway"));
    } else $this->render("admin/create");
  }
  function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("payment_gateway_settings", "create")) {
      $setting = $this->db->query("payment_gateway_settings")->condition("id", $id)->one();
      $this->redirect("admin/payment_gateways/settings/".$setting["payment_gateway_id"]);
    } else $this->render("admin/update");
  }
  function import() {
    $this->render("admin/import");
  }
}

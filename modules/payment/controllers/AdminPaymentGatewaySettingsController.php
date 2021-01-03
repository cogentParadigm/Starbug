<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\InputFilterInterface;

class AdminPaymentGatewaySettingsController extends Controller {
  /**
   * Input filter
   *
   * @var InputFilterInterface
   */
  protected $filter;
  public function __construct(DatabaseInterface $db, InputFilterInterface $filter) {
    $this->db = $db;
    $this->filter = $filter;
  }
  public function init() {
    $this->assign("model", "payment_gateway_settings");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    if ($this->db->success("payment_gateway_settings", "create")) {
      $gateway = $this->filter->normalize($this->request->getQueryParams()["gateway"]);
      $this->response->redirect("admin/payment_gateways/settings/".$gateway);
    } else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("payment_gateway_settings", "create")) {
      $setting = $this->db->query("payment_gateway_settings")->condition("id", $id)->one();
      $this->response->redirect("admin/payment_gateways/settings/".$setting["payment_gateway_id"]);
    } else $this->render("admin/update.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}

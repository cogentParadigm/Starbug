<?php
namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiShippingLinesController extends ApiController {
  public $model = "shipping_lines";
  public function __construct(IdentityInterface $user, Cart $cart) {
    $this->user = $user;
    $this->cart = $cart;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function cart() {
    $params = [];
    if (!$this->request->hasParameter("order")) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("ShippingLines", $params);
  }
  public function order() {
    $this->api->render("ShippingLines");
  }
  public function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) {
      $query->condition(
        $query->createCondition()
        ->condition("shipping_lines.orders_id.token", $this->request->getCookie("cid"))
        ->orCondition("shipping_lines.orders_id.owner", $this->user->userinfo("id"))
      );
    }
    return $query;
  }
}

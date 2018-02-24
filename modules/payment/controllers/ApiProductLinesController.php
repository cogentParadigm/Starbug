<?php
namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiProductLinesController extends ApiController {
  public $model = "product_lines";
  function __construct(IdentityInterface $user, Cart $cart) {
    $this->user = $user;
    $this->cart = $cart;
  }
  function admin() {
    $this->api->render("Admin");
  }
  function select() {
    $this->api->render("Select");
  }
  function cart() {
    $params = [];
    if (!$this->request->hasParameter("order")) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("ProductLines", $params);
  }
  function order() {
    $this->api->render("ProductLines");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) {
      $query->condition(
        $query->createCondition()
          ->condition("product_lines.orders_id.token", $this->request->getCookie("cid"))
          ->orCondition("product_lines.orders_id.owner", $this->user->userinfo("id"))
      );
    }
    return $query;
  }
}

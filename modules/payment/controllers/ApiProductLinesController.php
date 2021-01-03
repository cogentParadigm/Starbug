<?php
namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiProductLinesController extends ApiController {
  public $model = "product_lines";
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
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("ProductLines", $params);
  }
  public function order() {
    $this->api->render("ProductLines");
  }
  public function filterQuery($collection, $query, $ops) {
    $cid = $this->request->getCookieParams()["cid"];
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) {
      $query->condition(
        $query->createCondition()
          ->condition("product_lines.orders_id.token", $cid)
          ->orCondition("product_lines.orders_id.owner", $this->user->userinfo("id"))
      );
    }
    return $query;
  }
}

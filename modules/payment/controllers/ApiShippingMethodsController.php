<?php

namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiShippingMethodsController extends ApiController {
  public $model = "shipping_methods";
  public function __construct(SessionHandlerInterface $session, Cart $cart) {
    $this->session = $session;
    $this->cart = $cart;
  }
  public function admin() {
    $this->api->render("AdminShippingMethods");
  }
  public function select() {
    $params = [];
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("SelectShippingMethods", $params);
  }
}

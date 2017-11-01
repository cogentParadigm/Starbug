<?php

namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiShippingMethodsController extends ApiController {
  public $model = "shipping_methods";
  function __construct(IdentityInterface $user, Cart $cart) {
    $this->user = $user;
    $this->cart = $cart;
  }
  function admin() {
    $this->api->render("AdminShippingMethods");
  }
  function select() {
    $params = [];
    if (!$this->request->hasParameter("order")) {
      $params["order"] = $this->cart->get("id");
    }
    $this->api->render("SelectShippingMethods", $params);
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

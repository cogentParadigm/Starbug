<?php

namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiRequest;
use Starbug\Core\Controller\CollectionController;

class ApiShippingMethodsController extends CollectionController {
  public $model = "shipping_methods";
  public function __construct(ApiRequest $api, SessionHandlerInterface $session, Cart $cart) {
    parent::__construct($api);
    $this->session = $session;
    $this->cart = $cart;
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

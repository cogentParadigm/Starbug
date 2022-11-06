<?php

namespace Starbug\Payment;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiRequest;
use Starbug\Core\Controller\CollectionController;

class ApiShippingMethodsController extends CollectionController {
  public $model = "shipping_methods";
  public function __construct(ApiRequest $api, SessionHandlerInterface $session, Cart $cart, ServerRequestInterface $request) {
    parent::__construct($api);
    $this->session = $session;
    $this->cart = $cart;
    $this->request = $request;
  }
  public function select() {
    $params = [];
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    return $this->api->render(SelectShippingMethodsCollection::class, $params);
  }
}

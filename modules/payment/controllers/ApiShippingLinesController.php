<?php
namespace Starbug\Payment;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiRequest;
use Starbug\Core\Controller\CollectionController;

class ApiShippingLinesController extends CollectionController {
  public $model = "shipping_lines";
  public function __construct(ApiRequest $api, SessionHandlerInterface $session, Cart $cart, ServerRequestInterface $request) {
    parent::__construct($api);
    $this->session = $session;
    $this->cart = $cart;
    $this->request = $request;
  }
  public function cart() {
    $params = [];
    $queryParams = $this->request->getQueryParams();
    if (empty($queryParams["order"])) {
      $params["order"] = $this->cart->get("id");
    }
    return $this->api->render(ShippingLinesCollection::class, $params);
  }
  public function filterQuery($collection, $query, $ops) {
    $cid = $this->request->getCookieParams()["cid"];
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) {
      $query->condition(
        $query->createCondition()
        ->condition("shipping_lines.orders_id.token", $cid)
        ->orCondition("shipping_lines.orders_id.owner", $this->session->getUserId())
      );
    }
    return $query;
  }
}

<?php
namespace Starbug\Payment;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class Orders extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, ServerRequestInterface $request, Cart $cart) {
    parent::__construct($db, $models, $schemer);
    $this->request = $request;
    $this->cart = $cart;
  }

  public function create($order) {
    if (empty($order["id"])) {
      $order["token"] = $this->request->getCookieParams()["cid"];
    }
    $this->store($order);
  }

  public function post($action, $data = []) {
    $this->action = $action;
    if (in_array($action, ["checkout", "payment"]) && isset($data["id"]) && $this->cart->get("id") == $data["id"]) {
      $this->$action($data);
      return true;
    } else {
      return parent::post($action, $data);
    }
  }
}

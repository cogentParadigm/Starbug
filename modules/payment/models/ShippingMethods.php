<?php
namespace Starbug\Payment;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class ShippingMethods extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, Cart $cart) {
    parent::__construct($db, $models, $schemer);
    $this->cart = $cart;
  }

  public function add($method) {
    $method = $this->cart->selectShippingMethod($method);
  }
}

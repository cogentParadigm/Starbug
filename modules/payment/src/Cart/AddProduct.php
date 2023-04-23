<?php
namespace Starbug\Payment\Cart;

use Starbug\Db\DatabaseInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;

class AddProduct extends Save {
  protected $model = "products";
  public function __construct(DatabaseInterface $db, Cart $cart) {
    $this->db = $db;
    $this->cart = $cart;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->cart->addProduct($data);
    return $this->getErrorState($state);
  }
}

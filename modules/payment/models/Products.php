<?php
namespace Starbug\Payment;

use Starbug\Core\ProductsModel;

class Products extends ProductsModel {
  public function add($product) {
    $product = $this->cart->addProduct($product);
  }
}
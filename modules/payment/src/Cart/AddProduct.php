<?php
namespace Starbug\Payment\Cart;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;

class AddProduct extends Save {
  protected $model = "products";
  public function __construct(ModelFactoryInterface $models, Cart $cart) {
    $this->models = $models;
    $this->cart = $cart;
  }
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $product = $data->get();
    $this->cart->addProduct($product);
    return $this->getErrorState($state);
  }
}

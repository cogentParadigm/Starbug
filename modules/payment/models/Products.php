<?php
namespace Starbug\Payment;

use Starbug\Core\ProductsModel;

class Products extends ProductsModel {
  public function create($product) {
    $options = $product["options"];
    unset($product["options"]);
    $this->store($product);
    if (!$this->errors()) {
      $id = empty($product["id"]) ? $this->insert_id : $product["id"];
      foreach ($options as $option => $value) {
        $conditions = ["products_id" => $id, "options_id" => $option];
        $exists = $this->models->get("products_options")->query()->conditions($conditions)->one();
        if ($exists) {
          $this->models->get("products_options")->store(["id" => $exists["id"], "value" => $value]);
        } else {
          $this->models->get("products_options")->store($conditions + ["value" => $value]);
        }
      }
    }
  }
  public function add($product) {
    $product = $this->cart->addProduct($product);
  }
}

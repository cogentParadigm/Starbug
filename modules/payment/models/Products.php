<?php
namespace Starbug\Payment;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class Products extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, Cart $cart) {
    parent::__construct($db, $models, $schemer);
    $this->cart = $cart;
  }

  public function create($product) {
    $options = $product["options"] ?? [];
    unset($product["options"]);
    $this->store($product);
    if (!$this->errors()) {
      $id = empty($product["id"]) ? $this->db->getInsertId("products") : $product["id"];
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

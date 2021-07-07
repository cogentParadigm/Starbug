<?php
namespace Starbug\Payment\Product;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save as ParentOperation;

class Save extends ParentOperation {
  protected $model = "products";
  public function handle(array $product, BundleInterface $state): BundleInterface {
    $options = $product["options"] ?? [];
    unset($product["options"]);
    $this->db->store($this->model, $product);
    if (!$this->db->errors()) {
      $id = empty($product["id"]) ? $this->db->getInsertId("products") : $product["id"];
      foreach ($options as $option => $value) {
        $conditions = ["products_id" => $id, "options_id" => $option];
        $exists = $this->db->query("products_options")->conditions($conditions)->one();
        if ($exists) {
          $this->db->store("products_options", ["id" => $exists["id"], "value" => $value]);
        } else {
          $this->db->store("products_options", $conditions + ["value" => $value]);
        }
      }
    }
    return $this->getErrorState($state);
  }
}

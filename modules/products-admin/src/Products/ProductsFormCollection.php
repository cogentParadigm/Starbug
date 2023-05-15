<?php
namespace Starbug\Products\Admin\Products;

use Starbug\Core\FormCollection;

class ProductsFormCollection extends FormCollection {
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    $query->select("path.alias as path");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["options"] = [];
      $options = $this->db->query("products_options")
        ->select("products_options.*,options_id.slug")->condition("products_id", $row["id"])->all();
      foreach ($options as $option) {
        $row["options"][$option["slug"]] = $option["value"];
      }
    }
    return $rows;
  }
}

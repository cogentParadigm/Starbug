<?php
namespace Starbug\Payment;

use Starbug\Core\FormCollection;

class ProductsFormCollection extends FormCollection {
  public function build($query, $ops) {
    $query->select("path.alias as path");
    return parent::build($query, $ops);
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["options"] = [];
      $options = $this->db->query("products_options")
        ->select("options_id.slug")->condition("products_id", $row["id"])->all();
      foreach ($options as $option) {
        $row["options"][$option["slug"]] = $option["value"];
      }
    }
    return $rows;
  }
}

<?php
namespace Starbug\Payment;

use Starbug\Core\FormCollection;

class ProductsFormCollection extends FormCollection {
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["options"] = [];
      $options = $this->models->get("products_options")->query()
        ->select("options_id.slug")->condition("products_id", $row["id"])->all();
      foreach ($options as $option) {
        $row["options"][$option["slug"]] = $option["value"];
      }
    }
    return $rows;
  }
}

<?php
namespace Starbug\Payment;

use Starbug\Core\FormCollection;

class ProductConfigurationFormCollection extends FormCollection {
  public function build($query, $ops) {
    $this->ops = $ops;
    return parent::build($query, $ops);
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      if (!empty($this->ops["product_lines_id"])) {
        $options = $this->models->get("product_lines_options")->query()
          ->select("options_id.slug")->condition("product_lines_id", $this->ops["product_lines_id"])->all();
        foreach ($options as $option) {
          $row["options"][$option["slug"]] = $option["value"];
        }
      }
    }
    return $rows;
  }
}

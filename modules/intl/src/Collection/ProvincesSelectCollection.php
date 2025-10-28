<?php
namespace Starbug\Intl\Collection;

use Starbug\Db\Collection\SelectCollection;

class ProvincesSelectCollection extends SelectCollection {
  protected $model = "provinces";
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["attributes"]["country"])) {
      $ops["country"] = $ops["attributes"]["country"];
    }
    if (!empty($ops["country"])) {
      if (is_numeric($ops["country"])) {
        $query->condition("provinces.countries_id", $ops["country"]);
      } else {
        $query->condition("provinces.countries_id.code", $ops["country"]);
      }
    }
    return $query;
  }
}

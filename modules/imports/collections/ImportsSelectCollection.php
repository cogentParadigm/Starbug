<?php
namespace Starbug\Imports;

use Starbug\Core\SelectCollection;

class ImportsSelectCollection extends SelectCollection {
  protected $model = "imports";
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["id"])) {
      $ids = array_filter(explode(",", $ops["id"]), "is_numeric");
      $query->sort("FIELD(imports.id, ".implode(", ", $ids).")");
    }
    return $query;
  }
}

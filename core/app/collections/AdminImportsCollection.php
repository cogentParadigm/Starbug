<?php
namespace Starbug\Core;

class AdminImportsCollection extends AdminCollection {
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["model"])) {
      $query->condition("imports.model", $ops["model"]);
    }
    return $query;
  }
}

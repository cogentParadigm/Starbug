<?php
namespace Starbug\Imports\Admin;

use Starbug\Core\AdminCollection;

class ImportsCollection extends AdminCollection {
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["model"])) {
      $query->condition("imports.model", $ops["model"]);
    }
    return $query;
  }
}

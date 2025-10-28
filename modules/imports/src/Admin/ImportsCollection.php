<?php
namespace Starbug\Imports\Admin;

use Starbug\Admin\Db\Query\AdminCollection;

class ImportsCollection extends AdminCollection {
  protected $search_fields = "imports.name";
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["model"])) {
      $query->condition("imports.model", $ops["model"]);
    }
    return $query;
  }
}

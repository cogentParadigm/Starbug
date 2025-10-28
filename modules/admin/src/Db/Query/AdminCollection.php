<?php
namespace Starbug\Admin\Db\Query;

use Starbug\Db\Collection;

class AdminCollection extends Collection {
  public function build($query, $ops) {
    if (isset($ops["deleted"])) {
      $query->condition($query->model.".deleted", explode(",", $ops['deleted']));
    } else {
      $query->condition($query->model.".deleted", "0");
    }
    if (!empty($ops["sort"])) {
      $query->sort($ops["sort"]);
    }
    return $query;
  }
}

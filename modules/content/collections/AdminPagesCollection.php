<?php
namespace Starbug\Content;

use Starbug\Core\Collection;

class AdminPagesCollection extends Collection {
  public function build($query, $ops) {
    if (!empty($ops['type'])) {
      $query->condition($query->model.".type", $ops['type']);
    }
    if (isset($ops['published'])) $query->condition($query->model.".published", $ops['published']);
    if (isset($ops['deleted'])) $query->condition($query->model.".deleted", $ops["deleted"]);
    else $query->condition($query->model.".deleted", "0");
    if (empty($ops['orderby'])) $ops['orderby'] = "modified DESC, created DESC, title DESC";
    $query->sort($ops['orderby']);
    return $query;
  }
}

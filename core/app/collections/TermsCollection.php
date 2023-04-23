<?php
namespace Starbug\Core;

use Starbug\Db\Collection;

class TermsCollection extends Collection {
  public function build($query, $ops) {
    return $query;
  }
  public function filterQuery($query, $ops) {
    parent::filterQuery($query, $ops);
    if (!empty($ops['taxonomy'])) {
      $query->condition("terms.taxonomy", $ops['taxonomy']);
    }
    return $query;
  }
}

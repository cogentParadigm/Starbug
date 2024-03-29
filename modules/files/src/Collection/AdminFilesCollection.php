<?php
namespace Starbug\Files\Collection;

use Starbug\Db\Collection;

class AdminFilesCollection extends Collection {
  protected $model = "files";
  public function build($query, $ops) {
    $query->condition("files.deleted", "0");
    if (!empty($ops['category']) && is_numeric($ops['category'])) {
      $query->condition("category", $ops['category']);
    }
    return $query;
  }
}

<?php
namespace Starbug\Files;

use Starbug\Core\Collection;

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

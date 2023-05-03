<?php
namespace Starbug\Files\Collection;

use Starbug\Core\SelectCollection;

class FilesSelectCollection extends SelectCollection {
  protected $model = "files";
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    $query->select([
      "filename",
      "location",
      "mime_type",
      "size",
      "modified"
    ], "files");
    return $query;
  }
}

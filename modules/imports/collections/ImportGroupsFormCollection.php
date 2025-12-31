<?php
namespace Starbug\Imports;

use Starbug\Admin\Db\Query\FormCollection;

class ImportGroupsFormCollection extends FormCollection {
  protected $model = "import_groups";
  public function build($query, $ops) {
    if (empty($ops['action'])) {
      $ops['action'] = "create";
    }
    $query->action($ops['action'], "import_groups");
    $query->condition("import_groups.id", $ops['id']);
    $query->select("import_groups.*");
    $query->select("GROUP_CONCAT(import_groups.imports.id ORDER BY import_groups_imports_lookup.position) as imports");
    return $query;
  }
}

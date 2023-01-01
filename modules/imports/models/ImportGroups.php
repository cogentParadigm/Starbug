<?php
namespace Starbug\Spreadsheet;

use Starbug\Core\ImportGroupsModel;

class ImportGroups extends ImportGroupsModel {

  public function saveRun($group) {
    $this->create($group);
  }

  public function run($group) {
    $imports = $this->db->query("import_groups")
      ->condition("import_groups.id", $group['id'])
      ->select("import_groups.*")
      ->select("import_groups.imports.id as import")
      ->sort("import_groups_imports_lookup.position")
      ->all();
    foreach ($imports as $import) {
      if ($import["source"]) {
        $this->db->store("imports", ["id" => $import["import"], "source" => $import["source"]]);
      }
      $this->models->get("imports")->run(["id" => $import["import"]]);
    }
  }
}

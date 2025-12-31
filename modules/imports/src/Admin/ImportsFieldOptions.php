<?php
namespace Starbug\Imports\Admin;

use Starbug\Db\Schema\SchemerInterface;

class ImportsFieldOptions {
  public function __construct(SchemerInterface $schemer) {
    $this->schema = $schemer->getSchema();
  }
  public function __invoke($options) {
    $model = $options["model"];
    $columns = $this->schema->getColumn($model);
    $options = array_keys($columns);
    foreach ($columns as $name => $props) {
      if ($props["type"] == "int" && isset($props["references"]) && isset($props["operation"])) {
        $relatedModel = explode(" ", $props["references"])[0];
        $relatedCols = $this->__invoke(["model" => $relatedModel]);
        foreach ($relatedCols as $relatedCol) {
          $options[] = $name . ": " . $relatedCol;
        }
      }
    }
    return $options;
  }
}

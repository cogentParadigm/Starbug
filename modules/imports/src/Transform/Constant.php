<?php
namespace Starbug\Imports\Transform;

class Constant extends AbstractTransformer {
  public function transform($source, $dest, $options = []): array {
    $dest[$options["field"]] = $options["value"];
    return $dest;
  }
  public function getLabel($row): string {
    return "Set ".$row["field"]." to '".$row["value"]."'";
  }
}

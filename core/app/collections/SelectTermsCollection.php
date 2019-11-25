<?php
namespace Starbug\Core;

class SelectTermsCollection extends TermsTreeCollection {
  protected $optional = false;
  public function build($query, $ops) {
    parent::build($query, $ops);
    if (empty($ops['id']) && isset($ops["optional"])) {
      $this->optional = $ops["optional"];
    }
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $row) {
      $rows[$idx] = ["id" => $row["id"], "label" => $row["term"]];
    }
    if (false !== $this->optional) {
      array_unshift($rows, ["id" => "", "label" => $this->optional]);
    }
    return $rows;
  }
}

<?php
namespace Starbug\Core;

class SelectTermsCollection extends TermsTreeCollection {
  public function filterRows($rows) {
    foreach ($rows as $idx => $row) {
      $rows[$idx] = ["id" => $row["id"], "label" => $row["term"]];
    }
    return $rows;
  }
}

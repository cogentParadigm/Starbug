<?php
namespace Starbug\Core;

class TermsTreeCollection extends TermsCollection {
  protected $model = "terms";
  public function build($query, $ops) {
    $query->select("terms.*,(SELECT COUNT(*) FROM ".$this->db->prefix("terms")." as t WHERE t.parent=terms.id) as children");
    if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
    else $query->condition("terms.parent", 0);
    if (!empty($ops["exclude"])) {
      $query->condition("terms.slug", explode(",", $ops["exclude"]), "!=");
    }
    $query->sort("terms.position");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $depth = 0;
      if (!empty($row['term_path'])) {
        $tree = $row['term_path'];
        $depth = substr_count($tree, "-")-1;
      }
      if ($depth > 0) $row['term'] = str_pad(" ".$row['term'], strlen(" ".$row['term'])+$depth, "-", STR_PAD_LEFT);
    }
    return $rows;
  }
}

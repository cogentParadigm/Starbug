<?php
namespace Starbug\Content;

use Starbug\Core\MenuCollection as ParentCollection;

class MenuCollection extends ParentCollection {
  public function build($query, &$ops) {
    $query->select("menus.pages_id.title,menus.pages_id.breadcrumb,menus.pages_id.path.alias as path");
    return parent::build($query, $ops);
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      if (!empty($row['pages_id'])) {
        if (!empty($row["breadcrumb"])) $row["content"] = $row["breadcrumb"];
        elseif (!empty($row["title"])) $row["content"] = $row["title"];
        $row["href"] = $row["path"];
      }
    }
    return parent::filterRows($rows);
  }
}

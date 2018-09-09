<?php
namespace Starbug\Core;

class TermsTreeGrid extends GridDisplay {
  public $model = "terms";
  public $action = "tree";
  public function build_display($options) {
    $this->dnd();
    $this->attr('base_url', 'admin/taxonomies');
    $this->insert(0, ["id", "plugin" => "starbug.grid.columns.tree", "sortable" => "false"]);
    $this->add(["term", "sortable" => "false"]);
    $this->add(["position", "sortable" => "false"]);
  }
}

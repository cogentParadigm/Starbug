<?php
namespace Starbug\Core;

class TermsGrid extends GridDisplay {
  public $model = "terms";
  public $action = "admin";
  public function build_display($options) {
    $this->add("taxonomy");
    $this->add(["row_options", "plugin" => "starbug.grid.columns.taxonomy_options"]);
  }
}

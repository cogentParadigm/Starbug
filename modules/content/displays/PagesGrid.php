<?php
namespace Starbug\Content;

use Starbug\Core\GridDisplay;

class PagesGrid extends GridDisplay {
  public $model = "pages";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("title", "published", ["modified", "label" => "Last Modified"]);
  }
}

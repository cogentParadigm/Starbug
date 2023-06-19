<?php
namespace Starbug\Products\Admin\ProductTags;

use Starbug\Core\GridDisplay;

class ProductTagsGrid extends GridDisplay {
  public $model = "product_tags";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
  }
}

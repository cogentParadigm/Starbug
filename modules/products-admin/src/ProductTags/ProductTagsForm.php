<?php
namespace Starbug\Products\Admin\ProductTags;

use Starbug\Core\FormDisplay;

class ProductTagsForm extends FormDisplay {
  public $model = "product_tags";
  public $cancel_url = "admin/product-tags";
  public function buildDisplay($options) {
    $this->add("name");
  }
}

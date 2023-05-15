<?php
namespace Starbug\Products\Admin\ProductTypes;

use Starbug\Core\FormDisplay;

class ProductTypesForm extends FormDisplay {
  public $model = "product_types";
  public $cancel_url = "admin/product_types";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("slug");
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["content", "input_type" => "textarea"]);
  }
}

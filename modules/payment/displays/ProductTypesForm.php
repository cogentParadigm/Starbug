<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ProductTypesForm extends FormDisplay {
  public $model = "product_types";
  public $cancel_url = "admin/product_types";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("slug");
    $this->add("description");
    $this->add("content");
  }
}

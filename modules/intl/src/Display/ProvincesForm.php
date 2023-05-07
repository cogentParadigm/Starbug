<?php
namespace Starbug\Intl\Display;

use Starbug\Core\FormDisplay;

class ProvincesForm extends FormDisplay {
  public $model = "provinces";
  public $cancel_url = "admin/provinces";
  public function buildDisplay($options) {
    $this->add("countries_id");
    $this->add("name");
    $this->add("code");
  }
}

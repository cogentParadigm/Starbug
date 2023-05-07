<?php
namespace Starbug\Intl\Display;

use Starbug\Core\FormDisplay;

class CountriesForm extends FormDisplay {
  public $model = "countries";
  public $cancel_url = "admin/countries";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("code");
    $this->add("format");
    $this->add("upper");
    $this->add("require");
    $this->add("postal_code_prefix");
    $this->add("postal_code_format");
    $this->add("postal_code_label");
    $this->add("province_label");
    $this->add("postal_url");
  }
}

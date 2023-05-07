<?php
namespace Starbug\Log\Display;

use Starbug\Core\SearchForm;

class ErrorLogSearchForm extends SearchForm {
  public function buildDisplay($options) {
    parent::buildDisplay($options);
    $this->add(["level", "input_type" => "select", "nolabel" => true, "data-filter" => $this->model, "options" => "Any Level,Debug,Info,Notice,Warning,Error,Critical,Alert,Emergency", "values" => ",100,200,250,300,400,500,550,600"]);
  }
}

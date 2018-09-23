<?php
namespace Starbug\Core;

class SearchForm extends FormDisplay {
  public $method = "get";
  public $defaultAction = "search";
  public $submit_label = "Search";
  public function buildDisplay($options) {
    $this->attributes['class'][] = 'form-inline';
    $this->add(["keywords", "input_type" => "text", "nolabel"  => true, "data-filter" => $this->model]);
    $this->actions->add(["search", "class" => "btn-default"]);
    $this->actions->template = "inline";
  }
}

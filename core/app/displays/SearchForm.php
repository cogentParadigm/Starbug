<?php
namespace Starbug\Core;

class SearchForm extends FormDisplay {
  public $method = "get";
  public $default_action = "search";
  public $submit_label = "Search";
  function build_display($options) {
    $this->attributes['class'][] = 'form-inline';
    $this->add(["keywords", "input_type" => "text", "nolabel"  => true, "data-filter" => $this->model]);
    $this->actions->add(["search", "class" => "btn-default"]);
    $this->actions->template = "inline";
  }
}

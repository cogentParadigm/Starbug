<?php
namespace Starbug\Users\Display;

use Starbug\Core\SearchForm;

class UsersSearchForm extends SearchForm {
  protected function buildPrimaryControls() {
    parent::buildPrimaryControls();
    $this->add(["groups", "input_type" => "text", "nolabel" => true, "data-dojo-type" => "sb/form/MultipleSelect", "data-dojo-props" => "model:'terms', query:{taxonomy:'groups'}", "pane" => "keywords", "div" => "ml1"]);
    $this->add(["deleted", "input_type" => "select", "nolabel"  => true, "values" => ["0", "1", "0,1"], "options" => ["Active", "Deleted", "Any Status"], "default" => "0", "pane" => "keywords", "div" => "ml1"]);
  }
}

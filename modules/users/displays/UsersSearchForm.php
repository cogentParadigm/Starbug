<?php
namespace Starbug\Users;

use Starbug\Core\SearchForm;

class UsersSearchForm extends SearchForm {
  public function build($options = []) {
    parent::build($options);
    $this->add(["groups", "input_type" => "text", "nolabel" => true, "data-dojo-type" => "sb/form/MultipleSelect", "data-dojo-props" => "model:'terms', query:{taxonomy:'groups'}", "data-filter" => $this->model]);
    $this->add(["deleted", "input_type" => "select", "nolabel"  => true, "values" => ["0", "1", "0,1"], "options" => ["Active", "Deleted", "Any"], "default" => "0", "data-filter" => $this->model]);
  }
}

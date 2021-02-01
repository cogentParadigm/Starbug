<?php
namespace Starbug\Content;

use Starbug\Core\MenusForm as ParentForm;

class MenusForm extends ParentForm {
  public function buildDisplay($ops) {
    parent::buildDisplay($ops);
    $this->add(["pages_id", "pane" => "ml", "data-dojo-type" => "sb/form/Autocomplete", "data-dojo-props" => "model: 'pages'", "label" => "Page", "info" => "Select a page."]);
  }
}

<?php
namespace Starbug\Core;

class MenusForm extends FormDisplay {
  public $model = "menus";
  public $cancel_url = "admin/menus";
  public function buildDisplay($options) {
    $defaultMenu = $options["menu"] ?? "";
    $menu = $this->get("menu") ?? $defaultMenu;
    $this->layout->add(["top", "tl" => "div.col-md-6.col-6", "tr" => "div.col-md-6.col-6"]);
    $this->layout->add(["middle", "ml" => "div.col-md-6.col-6", "mr" => "div.col-md-6.col-6"]);
    $this->layout->add(["bottom", "bl" => "div.col-md-6.col-6", "br" => "div.col-md-6.col-6"]);
    if (!empty($options['new'])) {
      $this->add(["menu", "pane" => "tl", "input_type" => "text"]);
    } else {
      $this->add(["menu", "input_type" => "hidden", "pane" => "tl", "default" => $defaultMenu]);
    }
    $this->add(["parent", "data-dojo-type" => "sb/form/Autocomplete", "data-dojo-props" => "model:'menus', query: {menu: '".$menu."', optional: ''}", "pane" => "tl", "info" => "Leave empty to place the item at the top level."]);
    $this->add(["position", "pane" => "tr", "info" => "Enter 1 for the first position, leave empty for the last."]);
    $this->add(["href", "pane" => "mr", "label" => "URL", "info" => "Enter a URL manually."]);
    $this->add(["content", "pane" => "bl", "info" => "Override the link text."]);
    $this->add(["icon", "label" => "Icon class", "pane" => "bl"]);
    $this->add(["target", "pane" => "br", "input_type" => "checkbox", "label" => "Open in new tab/window", "value" => "_blank", "unchecked" => ""]);
    $this->add(["template", "pane" => "br", "input_type" => "checkbox", "label" => "Divider", "value" => "divider", "unchecked" => ""]);
  }
}

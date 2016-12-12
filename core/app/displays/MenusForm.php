<?php
namespace Starbug\Core;
class MenusForm extends FormDisplay {
	public $model = "menus";
	public $cancel_url = "admin/menus";
	function build_display($ops) {
		$this->layout->add(["top", "tl" => "div.col-md-6", "tr" => "div.col-md-6"]);
		$this->layout->add(["middle", "ml" => "div.col-md-6", "mr" => "div.col-md-6"]);
		$this->layout->add(["bottom", "bl" => "div.col-md-6", "br" => "div.col-md-6"]);
		$this->add(["menu", "input_type" => "hidden", "pane" => "tl", "default" => $ops['menu']]);
		$this->add(["parent", "input_type" => "autocomplete", "from" => "menus", "pane" => "tl", "info" => "Leave empty to place the item at the top level."]);
		$this->add(["position", "pane" => "tr", "info" => "Enter 1 for the first position, leave empty for the last."]);
		$this->add(["href", "pane" => "mr", "label" => "URL", "info" => "Enter a URL manually."]);
		$this->add(["content", "pane" => "bl", "info" => "Override the link text."]);
		$this->add(["target", "pane" => "br", "input_type" => "checkbox", "label" => "Open in new tab/window", "value" => "_blank"]);
		$this->add(["template", "pane" => "br", "input_type" => "checkbox", "label" => "Divider", "value" => "divider"]);
	}
}
?>

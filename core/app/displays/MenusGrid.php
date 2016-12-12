<?php
namespace Starbug\Core;
class MenusGrid extends GridDisplay {
	public $model = "menus";
	public $action = "admin";
	public function build_display($options) {
		$this->add("menu");
		$this->add(["row_options", "plugin" => "starbug.grid.columns.menu_options"]);
	}
}
?>

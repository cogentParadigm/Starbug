<?php
namespace Starbug\Core;
class MenusTreeGrid extends GridDisplay {
	public $model = "menus";
	public $action = "tree";
	public function build_display($options) {
		$this->dnd();
		$this->attr('base_url', 'admin/menus');
		$this->insert(0, "id  plugin:starbug.grid.columns.tree  sortable:false");
		$this->add("content  label:Title  plugin:starbug.grid.columns.html  sortable:false", "position  sortable:false");
	}
}
?>

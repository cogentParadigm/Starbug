<?php
/**
 * menus model
 * @ingroup models
 */
class Menus extends MenusModel {

	function create($menu) {
		$this->store($menu);
	}
	
	function add_uri($menu) {
		store("uris_menus", "menus_id:$menu[id]  uris_id:$menu[uris_id]  position:  parent:$menu[parent]");
	}

	function delete($menu) {
		return $this->remove('id='.$menu['id']);
	}

}
?>

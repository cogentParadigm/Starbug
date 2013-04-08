<?php
/**
 * menus model
 * @ingroup models
 */
class Menus {

	function create($menu) {
		if (!isset($menu['position'])) $menu['position'] = "";
		if (!isset($menu['template'])) $menu['template'] = "";
		if (!isset($menu['target'])) $menu['target'] = "";
		$this->store($menu);
	}

	function delete($menu) {
		return $this->remove('id='.$menu['id']);
	}

	function query_admin($query) {
		$query['select'] = "DISTINCT menu";
		$query['where'][] = '!(menus.status & 1)';
		return $query;
	}

}
?>

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
		return $this->remove('id:'.$menu['id']);
	}

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->select("DISTINCT menu");
		return $query;
	}

}
?>

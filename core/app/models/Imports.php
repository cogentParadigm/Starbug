<?php
/**
 * imports model
 * @ingroup models
 */
class Imports {

	function create($import) {
		$this->store($import);
	}

	/******************************************************************
	 * Query functions
	 *****************************************************************/

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		if (!empty($ops['model'])) {
			$query->condition("imports.model", $ops['model']);
		}
    return $query;
  }

	function query_filters($action, $query, $ops) {
		if (!logged_in("root") && !logged_in("admin")) $query->action("read");
		return $query;
	}

	/******************************************************************
	 * Display functions
	 *****************************************************************/

	function display_admin($display, $ops) {
		$display->add("id");
	}

}
?>

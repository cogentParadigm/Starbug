<?php
/**
 * imports_fields model
 * @ingroup models
 */
class Imports_fields {

	function create($imports_field) {
		$this->store($imports_field);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

  function query_admin($query, &$ops) {
    $query = parent::query_admin($query, $ops);
    return $query;
  }

	function query_filters($action, $query, $ops) {
		if (!logged_in("root") && !logged_in("admin")) $query->action("read");
		return $query;
	}

	function query_select($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		} else {
			$query->condition("imports_fields.statuses.slug", "deleted", "!=", array("ornull" => true));
		}
		$query->select("imports_fields.id");
		$query->select("CONCAT(imports_fields.source, ' => ', imports_fields.destination, CASE WHEN update_key=1 THEN ' (update key)' ELSE '' END) as label");
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

<?php
/**
 * provinces model
 * @ingroup models
 */
class Provinces {

	function create($province) {
		$this->store($province);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

  function query_admin($query, &$ops) {
    $query = parent::query_admin($query, $ops);
    return $query;
  }

	function query_select($query, &$ops) {
		 if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		}
		$query->select("provinces.name as id");
		$query->select("provinces.name as label");
		$query->sort("provinces.name");
		if (!empty($ops['attributes']['country'])) {
			$query->condition("provinces.countries_id.code", $ops['attributes']['country']);
		}
		return $query;
	}

  function query_filters($action, $query, $ops) {
      if (!logged_in("root") && !logged_in("admin") && $action != "select") $query->action("read");
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

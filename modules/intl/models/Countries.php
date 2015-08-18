<?php
/**
 * countries model
 * @ingroup models
 */
class Countries {

	function create($country) {
		$this->store($country);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

  function query_admin($query, &$ops) {
    $query = parent::query_admin($query, $ops);
    return $query;
  }

  function query_filters($action, $query, $ops) {
      return $query;
  }

	function query_select($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		}

		$query->select("countries.name as id");
		$query->select("countries.name as label");
		$query->sort("countries.name");
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

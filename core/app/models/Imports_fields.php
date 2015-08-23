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

	/******************************************************************
	 * Display functions
	 *****************************************************************/

	function display_admin($display, $ops) {
	    $display->add("id");
  	}

}
?>
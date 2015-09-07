<?php
/**
 * entities model
 * @ingroup models
 */
namespace Starbug\Core;
class Entities extends EntitiesModel {

	function create($entitie) {
		$this->store($entitie);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

  function query_admin($query, &$ops) {
    $query = parent::query_admin($query, $ops);
    return $query;
  }

	function query_model($query, &$ops) {
		$query->select("entities.name as id,entities.label");
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

<?php
/**
 * languages model
 * @ingroup models
 */
class Languages {

	/******************************************************************
	 * Query functions
	 *****************************************************************/

	function query_admin($query, &$ops) {
		$query->sort("languages.name");
		return $query;
	}

	function query_select($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		}
		$query->select("languages.id");
		$query->select("languages.name as label");
		$query->sort("languages.name");
		return $query;
	}
}

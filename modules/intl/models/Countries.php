<?php
/**
 * countries model
 * @ingroup models
 */
namespace Starbug\Intl;
use Starbug\Core\CountriesModel;
class Countries extends CountriesModel {

	function create($country) {
		$this->store($country);
	}

	/******************************************************************
	 * Query functions
	 *****************************************************************/

	function query_select($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($query->model.".id", explode(",", $ops['id']));
		}

		$query->select("countries.id");
		$query->select("countries.name as label");
		$query->sort("countries.name");
		return $query;
	}
}

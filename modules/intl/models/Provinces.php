<?php
/**
 * provinces model
 * @ingroup models
 */
namespace Starbug\Intl;
use Starbug\Core\ProvincesModel;
class Provinces extends ProvincesModel {

	function create($province) {
		$this->store($province);
	}

  /******************************************************************
	 * Query functions
	 *****************************************************************/

	function filterQuery($collection, $query, &$ops) {
		$query->sort("provinces.name");
		if (!empty($ops['attributes']['country'])) {
			$query->condition("provinces.countries_id.code", $ops['attributes']['country']);
		}
		return $query;
	}

}
?>

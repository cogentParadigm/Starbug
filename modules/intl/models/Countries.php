<?php
namespace Starbug\Intl;

use Starbug\Core\CountriesModel;

class Countries extends CountriesModel {

  public function create($country) {
    $this->store($country);
  }

  /******************************************************************
   * Query functions
   *****************************************************************/

  public function query_select($query, &$ops) {
    if (!empty($ops['id'])) {
      $query->condition($query->model.".id", explode(",", $ops['id']));
    }

    $query->select("countries.id");
    $query->select("countries.name as label");
    $query->sort("countries.name");
    return $query;
  }
}

<?php
namespace Starbug\Intl;

use Starbug\Core\Table;

class Provinces extends Table {

  /******************************************************************
   * Query functions
   *****************************************************************/

  public function filterQuery($collection, $query, $ops) {
    $query->sort("provinces.name");
    if (!empty($ops['attributes']['country'])) {
      $query->condition("provinces.countries_id.code", $ops['attributes']['country']);
    }
    return $query;
  }
}

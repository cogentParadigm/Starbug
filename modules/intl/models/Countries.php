<?php
namespace Starbug\Intl;

use Starbug\Core\CountriesModel;

class Countries extends CountriesModel {

  public function create($country) {
    $this->store($country);
  }
}

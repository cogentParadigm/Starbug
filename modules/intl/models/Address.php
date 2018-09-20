<?php
namespace Starbug\Intl;

use Starbug\Core\AddressModel;

class Address extends AddressModel {

  public $map = [
    'N' => 'recipient',
    'O' => 'organization',
    'A' => 'address1',
    'D' => 'district',
    'C' => 'locality',
    'S' => 'administrative_area',
    'Z' => 'postal_code',
    'X' => 'sorting_code'
  ];

  public function create($address) {
    $country = $this->query("countries")->condition("id", $address['country'])->one();
    $req = str_split($country['require']);
    foreach ($req as $token) {
      if (!in_array($token, ['N', 'A']) && empty($address[$this->map[$token]])) $this->error("This field is required", $this->map[$token]);
    }
    $this->store($address);
  }

  function format($address, $country = false) {
    if (is_numeric($address)) $address = $this->query("address")->condition("id", $address)->one();
    if (!$country) $country = $this->query("countries")->condition("id", $address['country'])->one();
    if (is_numeric($address['administrative_area'])) {
      $region = $this->query("provinces")->condition("id", $address['administrative_area'])->one();
        $address['administrative_area'] = $region['name'];
    }
    $text = $country['format'];

    $text = str_replace("%n", "<br/>", $text);

    $search = [];
    $replace = [];
    foreach ($this->map as $k => $v) {
      $search[] = '%'.$k;
      $replace[] = $address[$v];
    }
    $text = str_replace($search, $replace, $text);
    $text = str_replace(["<br/><br/>", "  "], ["<br/>", " "], $text);
    return $text."<br/>".$country['name'];
  }
}

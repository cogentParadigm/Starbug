<?php
namespace Starbug\Intl;

use Starbug\Core\DatabaseInterface;

class AddressFormatter {
  protected $map = [
    'N' => 'recipient',
    'O' => 'organization',
    'A' => 'address1',
    'D' => 'district',
    'C' => 'locality',
    'S' => 'administrative_area',
    'Z' => 'postal_code',
    'X' => 'sorting_code'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function format($address, $country = false) {
    if (is_numeric($address)) $address = $this->db->query("address")->condition("id", $address)->one();
    if (!$country) $country = $this->db->query("countries")->condition("id", $address['country'])->one();
    if (is_numeric($address['administrative_area'])) {
      $region = $this->db->query("provinces")->condition("id", $address['administrative_area'])->one();
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

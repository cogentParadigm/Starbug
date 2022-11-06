<?php
namespace Starbug\Intl\Address;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save as ParentOperation;

class Save extends ParentOperation {
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

  public function handle(array $address, BundleInterface $state): BundleInterface {
    $country = $this->db->query("countries")->condition("id", $address['country'])->one();
    $req = str_split($country['require']);
    foreach ($req as $token) {
      if (!in_array($token, ['N', 'A']) && empty($address[$this->map[$token]])) {
        $this->db->error("This field is required", $this->map[$token], $this->model);
      }
    }
    $this->db->store($this->model, $address);
    return $this->getErrorState($state);
  }
}

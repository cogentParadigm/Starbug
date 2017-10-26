<?php
namespace Starbug\Intl;
use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;
class SelectAddressCollection extends Collection {
  public function __construct(ModelFactoryInterface $models, AddressFormatter $address) {
    $this->models = $models;
    $this->address = $address;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $row) {
      $rows[$idx] = [
        "id" => $row["id"],
        "label" => $this->address->format($row)
      ];
    }
    return $rows;
  }
}

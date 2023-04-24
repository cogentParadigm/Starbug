<?php
namespace Starbug\Intl;

use Starbug\Db\Collection;
use Starbug\Db\DatabaseInterface;

class SelectAddressCollection extends Collection {
  public function __construct(DatabaseInterface $db, AddressFormatter $address) {
    $this->db = $db;
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

<?php
namespace Starbug\Settings\Admin;

use Starbug\Db\Operation\Save;
use Starbug\Bundle\BundleInterface;

class SaveSettings extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    foreach ($data as $name => $value) {
      $this->db->store("settings", ["value" => $value], ["name" => $name]);
    }
    return $this->getErrorState($state);
  }
}

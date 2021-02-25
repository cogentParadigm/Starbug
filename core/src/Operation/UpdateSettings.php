<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class UpdateSettings extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    foreach ($data as $name => $value) {
      $this->store(["value" => $value], ["name" => $name]);
    }
    return $this->getErrorState($state);
  }
}

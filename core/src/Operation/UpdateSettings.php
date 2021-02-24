<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class UpdateSettings extends Save {
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    foreach ($data->get() as $name => $value) {
      $this->store(["value" => $value], ["name" => $name]);
    }
    return $this->getErrorState($state);
  }
}

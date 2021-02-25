<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class SoftDelete extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->store(["id" => $data["id"], "deleted" => "1"]);
    return $this->getErrorState($state);
  }
}

<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class Delete extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->remove($data["id"]);
    return $this->getErrorState($state);
  }
}

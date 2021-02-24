<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class Delete extends Save {
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $this->remove($data->get("id"));
    return $this->getErrorState($state);
  }
}

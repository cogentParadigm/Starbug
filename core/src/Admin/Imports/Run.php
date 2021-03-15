<?php
namespace Starbug\Core\Admin\Imports;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class Run extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->run($data);
    return $this->getErrorState($state);
  }
}

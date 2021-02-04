<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class SoftDelete extends Save {
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $this->store($this->model, ["id" => $data->get($this->model, "id"), "deleted" => "1"]);
    return $this->getErrorState($state);
  }
}

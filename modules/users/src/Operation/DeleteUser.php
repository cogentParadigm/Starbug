<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;

class DeleteUser extends Save {
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function handle(array $user, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $this->store(["id" => $user['id'], "deleted" => "1"]);
    return $this->getErrorState($state);
  }
}

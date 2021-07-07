<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class DeleteUser extends Save {
  public function handle(array $user, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $this->db->store("users", ["id" => $user['id'], "deleted" => "1"]);
    return $this->getErrorState($state);
  }
}

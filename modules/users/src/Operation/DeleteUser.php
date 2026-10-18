<?php
namespace Starbug\Users\Operation;

use Starbug\Db\Operation\Save;
use Starbug\Bundle\BundleInterface;

class DeleteUser extends Save {
  public function handle(array $user, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $this->db->store("users", ["id" => $user['id'], "deleted" => "1"]);
    return $this->getErrorState($state);
  }
}

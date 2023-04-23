<?php
namespace Starbug\Menus\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class DeleteMenus extends Save {
  protected $model = "menus";
  public function handle(array $data, BundleInterface $state): BundleInterface {
    if (empty($data["id"]) && !empty($data["menu"])) {
      $this->db->remove($this->model, ["menu" => $data["menu"]]);
    } else {
      $this->db->remove($this->model, ["id" => $data["id"]]);
    }
    return $this->getErrorState($state);
  }
}

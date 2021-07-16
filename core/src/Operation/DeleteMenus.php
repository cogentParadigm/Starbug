<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

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

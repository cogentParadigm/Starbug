<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;

class DeleteTerms extends Save {
  protected $model = "terms";
  public function handle(array $data, BundleInterface $state): BundleInterface {
    if (empty($data["id"]) && !empty($data["taxonomy"])) {
      $this->db->remove($this->model, ["taxonomy" => $data["taxonomy"]]);
    } else {
      $this->db->remove($this->model, ["id" => $data["id"]]);
    }
    return $this->getErrorState($state);
  }
}

<?php
namespace Starbug\App;

use Starbug\Db\Collection;

class AdminProductOptionsCollection extends Collection {
  protected $model = "product_options";
  public function build($query, $ops) {
    if (!empty($ops["product_types_id"])) {
      $query->condition("product_options.product_types_id", $ops["product_types_id"]);
    }
    if (!empty($ops["parent"])) {
      $query->condition("product_options.parent", $ops["parent"]);
    } else {
      $query->condition("product_options.parent", "0");
    }
    $query->select("(SELECT COUNT(*) FROM ".$this->db->prefix("product_options")." WHERE parent=product_options.id) as children");
    $query->sort("product_options.position");
    return parent::build($query, $ops);
  }
}

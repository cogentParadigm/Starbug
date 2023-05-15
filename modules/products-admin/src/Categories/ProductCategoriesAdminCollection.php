<?php
namespace Starbug\Products\Admin\Categories;

use Starbug\Core\AdminCollection as ParentCollection;

class ProductCategoriesAdminCollection extends ParentCollection {
  protected $model = "product_categories";
  public function build($query, $ops) {
    $query->select("product_categories.*");
    if (!empty($ops["parent"])) {
      $query->condition("product_categories.parent", $ops["parent"]);
    } else {
      $query->condition("product_categories.parent", "NULL");
    }
    $query->select(
      "(SELECT COUNT(*) FROM ".$this->db->prefix("product_categories")." WHERE parent=product_categories.id) as children"
    );
    $query->sort("product_categories.position");
    return parent::build($query, $ops);
  }
}

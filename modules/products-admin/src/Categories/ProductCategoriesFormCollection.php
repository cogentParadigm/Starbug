<?php
namespace Starbug\Products\Admin\Categories;

use Starbug\Display\Collection\FormCollection;

class ProductCategoriesFormCollection extends FormCollection {
  protected $model = "product_categories";
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    $query->select("path.alias as path");
    return $query;
  }
}

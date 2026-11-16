<?php
namespace Starbug\Users\Admin\Controller;

use Starbug\Api\Controller\CollectionController;

class ApiUsersController extends CollectionController {
  protected $model = "users";
  public function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

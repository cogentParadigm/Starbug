<?php
namespace Starbug\Users\Controller;

use Starbug\Api\Controller\CollectionController;

class ApiUsersController extends CollectionController {
  protected $model = "users";
  public function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

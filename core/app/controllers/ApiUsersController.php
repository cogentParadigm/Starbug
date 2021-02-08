<?php
namespace Starbug\Core;

use Starbug\Core\Controller\CollectionController;

class ApiUsersController extends CollectionController {
  protected $model = "users";
  public function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

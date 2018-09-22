<?php
namespace Starbug\Core;

class ApiUsersController extends ApiController {
  public $model = "users";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminUsers");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
  public function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

<?php
namespace Starbug\Core;

class ApiUsersController extends ApiController {
  public $model = "users";
  function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  function admin() {
    $this->api->render("AdminUsers");
  }
  function select() {
    $this->api->render("Select");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
  function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

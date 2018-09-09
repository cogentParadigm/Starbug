<?php
namespace Starbug\Core;

class ApiMenusController extends ApiController {
  public $model = "menus";
  function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  function admin() {
    $this->api->render("AdminMenus");
  }
  function select() {
    $this->api->render("Select");
  }
  function tree() {
    $this->api->render("MenusTree");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

<?php
namespace Starbug\Core;

class ApiMenusController extends ApiController {
  public $model = "menus";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminMenus");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function tree() {
    $this->api->render("MenusTree");
  }
  public function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

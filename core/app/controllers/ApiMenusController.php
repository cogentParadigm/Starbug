<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiMenusController extends ApiController {
  public $model = "menus";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    return $this->api->render("AdminMenus");
  }
  public function select() {
    return $this->api->render("Select");
  }
  public function tree() {
    return $this->api->render("MenusTree");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

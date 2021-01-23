<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiMenusController extends ApiController {
  public $model = "menus";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
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
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

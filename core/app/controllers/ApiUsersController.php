<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiUsersController extends ApiController {
  public $model = "users";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    return $this->api->render("AdminUsers");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
  public function filterRow($collection, $row) {
    unset($row['password']);
    return $row;
  }
}

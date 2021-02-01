<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiImportsController extends ApiController {
  public $model = "imports";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    return $this->api->render("Admin");
  }
  public function select() {
    return $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    if (!empty($ops['model'])) {
      $query->condition("imports.model", $ops['model']);
    }
    return $query;
  }
}

<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiImportsFieldsController extends ApiController {
  public $model = "imports_fields";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

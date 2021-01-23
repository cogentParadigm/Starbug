<?php

namespace Starbug\Log;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiErrorLogController extends ApiController {
  public $model = "error_log";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("AdminErrorLog");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

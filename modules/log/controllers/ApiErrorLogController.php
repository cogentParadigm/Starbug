<?php

namespace Starbug\Log;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiErrorLogController extends ApiController {
  public $model = "error_log";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminErrorLog");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

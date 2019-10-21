<?php
namespace Starbug\Core;

class ApiImportsController extends ApiController {
  public $model = "imports";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    if (!empty($ops['model'])) {
      $query->condition("imports.model", $ops['model']);
    }
    return $query;
  }
}

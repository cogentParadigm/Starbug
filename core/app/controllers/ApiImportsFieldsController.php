<?php
namespace Starbug\Core;

class ApiImportsFieldsController extends ApiController {
  public $model = "imports_fields";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

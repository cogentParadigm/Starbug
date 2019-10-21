<?php
namespace Starbug\Content;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiPagesController extends ApiController {
  public $model = "pages";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminPages");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

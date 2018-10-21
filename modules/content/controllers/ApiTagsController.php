<?php
namespace Starbug\Content;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiTagsController extends ApiController {
  public $model = "tags";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminTags");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

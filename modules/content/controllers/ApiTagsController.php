<?php
namespace Starbug\Content;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiTagsController extends ApiController {
  public $model = "tags";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("AdminTags");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

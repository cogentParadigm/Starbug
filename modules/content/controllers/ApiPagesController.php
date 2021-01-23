<?php
namespace Starbug\Content;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiPagesController extends ApiController {
  public $model = "pages";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("AdminPages");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

<?php
namespace Starbug\Intl;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiAddressController extends ApiController {
  public $model = "address";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("SelectAddress");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

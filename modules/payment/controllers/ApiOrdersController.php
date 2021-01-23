<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiOrdersController extends ApiController {
  public $model = "orders";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("AdminOrders");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

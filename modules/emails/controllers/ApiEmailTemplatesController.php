<?php
namespace Starbug\Emails;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;

class ApiEmailTemplatesController extends ApiController {
  public $model = "email_templates";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("Select");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

<?php
namespace Starbug\Emails;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiEmailTemplatesController extends ApiController {
  public $model = "email_templates";
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
    return $query;
  }
}

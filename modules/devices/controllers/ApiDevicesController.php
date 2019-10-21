<?php
namespace Starbug\Devices;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiDevicesController extends ApiController {
  public $model = "devices";
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

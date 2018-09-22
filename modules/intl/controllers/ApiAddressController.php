<?php
namespace Starbug\Intl;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiAddressController extends ApiController {
  public $model = "address";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("Admin");
  }
  public function select() {
    $this->api->render("SelectAddress");
  }
  public function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

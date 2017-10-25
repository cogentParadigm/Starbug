<?php
namespace Starbug\Intl;
use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;
class ApiAddressController extends ApiController {
  public $model = "address";
  function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  function admin() {
    $this->api->render("Admin");
  }
  function select() {
    $this->api->render("Select");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}

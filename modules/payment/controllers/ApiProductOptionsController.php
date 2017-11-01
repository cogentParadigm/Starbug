<?php

namespace Starbug\Payment;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiProductOptionsController extends ApiController {
  public $model = "product_options";
  function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  function admin() {
    $this->api->render("AdminProductOptions");
  }
  function select() {
    $this->api->render("Select");
  }
  function filterQuery($collection, $query, &$ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}
?>
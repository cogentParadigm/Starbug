<?php
namespace Starbug\Spreadsheet;

use Starbug\Core\ApiController;
use Starbug\Core\IdentityInterface;

class ApiImportGroupsController extends ApiController {
  public $model = "import_groups";
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
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) {
      $query->action("read");
    }
    return $query;
  }
}

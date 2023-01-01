<?php
namespace Starbug\Spreadsheet;

use Starbug\Core\ApiImportsController as ParentController;

class ApiImportsController extends ParentController {
  public function select() {
    $this->api->render("ImportsSelect");
  }
}

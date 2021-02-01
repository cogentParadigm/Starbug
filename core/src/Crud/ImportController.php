<?php
namespace Starbug\Core\Crud;

use Starbug\Core\Controller;

class ImportController extends Controller {
  public function __invoke($model) {
    return $this->render("admin/import.html", ["model" => $model]);
  }
}

<?php
namespace Starbug\Core\Crud;

use Starbug\Core\Controller;

class CreateController extends Controller {
  public function __invoke($model) {
    return $this->render("admin/create.html", ["model" => $model]);
  }
}

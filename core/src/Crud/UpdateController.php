<?php
namespace Starbug\Core\Crud;

use Starbug\Core\Controller;

class UpdateController extends Controller {
  public function __invoke($model, $id) {
    return $this->render("admin/update.html", ["model" => $model, "id" => $id]);
  }
}

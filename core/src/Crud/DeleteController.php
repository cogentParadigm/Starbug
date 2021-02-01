<?php
namespace Starbug\Core\Crud;

use Starbug\Core\Controller;

class DeleteController extends Controller {
  public function __invoke($model, $id) {
    return $this->render("admin/delete.html", ["model" => $model, "id" => $id]);
  }
}

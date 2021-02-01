<?php
namespace Starbug\Core\Crud;

use Starbug\Core\Controller;

class ListController extends Controller {
  public function __invoke($model) {
    return $this->render("admin/list.html", ["model" => $model]);
  }
}

<?php
namespace Starbug\Content;

use Starbug\Core\Controller;

class PagesController extends Controller {
  public $routes = [
    "view" => "view/{id}"
  ];
  public function view($id) {
    $this->render("blocks.html", ["region" => "content", "id" => $id], ["scope" => "templates"]);
  }
}

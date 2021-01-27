<?php
namespace Starbug\App\Page;

use Starbug\Core\Controller;

class HomeController extends Controller {
  public function defaultAction() {
    $this->render("home.html");
  }
}

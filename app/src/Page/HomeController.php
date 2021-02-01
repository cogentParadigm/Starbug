<?php
namespace Starbug\App\Page;

use Starbug\Core\Controller;

class HomeController extends Controller {
  public function __invoke() {
    return $this->render("home.html");
  }
}

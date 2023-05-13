<?php
namespace Starbug\App\Page;

use Starbug\Routing\Controller;

class HomeController extends Controller {
  public function __invoke() {
    return $this->render("home.html");
  }
}

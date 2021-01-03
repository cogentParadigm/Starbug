<?php
namespace Starbug\Core;

class AdminController extends Controller {
  public function defaultAction() {
    return $this->render("admin.html");
  }
}

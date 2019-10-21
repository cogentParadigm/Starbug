<?php
namespace Starbug\Core;

class AdminSettingsController extends Controller {
  public function init() {
    $this->assign("model", "settings");
  }
  public function defaultAction() {
    $this->render("settings.html");
  }
}

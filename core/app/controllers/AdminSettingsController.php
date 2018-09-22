<?php
namespace Starbug\Core;

class AdminSettingsController extends Controller {
  public function init() {
    $this->assign("model", "settings");
  }
  public function default_action() {
    $this->render("settings.html");
  }
}

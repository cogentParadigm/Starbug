<?php
namespace Starbug\Core;

class AdminSettingsController extends Controller {
  function init() {
    $this->assign("model", "settings");
  }
  function default_action() {
    $this->render("settings.html");
  }
}

<?php

namespace Starbug\Log;

use Starbug\Core\Controller;

class AdminErrorLogController extends Controller {
  public function init() {
    $this->assign("model", "error_log");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
}

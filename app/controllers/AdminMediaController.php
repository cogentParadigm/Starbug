<?php
namespace Starbug\App;

use Starbug\Core\Controller;

class AdminMediaController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function init() {
    $this->assign("model", "files");
  }
  public function default_action() {
    $this->response->template = "media-browser.html";
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->assign("action", "update");
    $this->render("admin/update.html");
  }
}

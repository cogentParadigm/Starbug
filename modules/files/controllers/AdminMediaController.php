<?php
namespace Starbug\Files;

use Starbug\Core\Controller;

class AdminMediaController extends Controller {
  public function init() {
    $this->assign("model", "files");
  }
  public function defaultAction() {
    $this->response->template = "media-browser.html";
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->assign("action", "update");
    $this->render("admin/update.html");
  }
}

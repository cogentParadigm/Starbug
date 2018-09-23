<?php
namespace Starbug\Core;

class ViewsController extends Controller {
  public function show() {
    $this->render($this->response->path.".html");
  }
}

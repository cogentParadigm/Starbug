<?php
namespace Starbug\Core;

class ViewsController extends Controller {
  public function show() {
    $this->render($this->request->getUri()->getPath().".html");
  }
}

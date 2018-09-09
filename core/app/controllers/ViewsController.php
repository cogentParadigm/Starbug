<?php
namespace Starbug\Core;

class ViewsController extends Controller {
  function show() {
    $this->render($this->response->path.".html");
  }
}

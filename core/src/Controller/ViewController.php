<?php
namespace Starbug\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Controller;

class ViewController extends Controller {
  public function __invoke($view, ServerRequestInterface $request): ResponseInterface {
    $arguments = $request->getAttribute("route")->getOptions();
    return $this->render($view, $arguments);
  }
}
